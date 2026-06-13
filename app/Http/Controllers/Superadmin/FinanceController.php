<?php

namespace App\Http\Controllers\Superadmin;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Enums\CommissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $timezone = 'Asia/Makassar';
        $now      = Carbon::now($timezone);

        // ── Filters ───────────────────────────────────────────────────────────
        $filterType   = $request->get('type', 'all');
        $filterStatus = $request->get('status', 'all');
        $filterMonth  = $request->get('month'); // YYYY-MM

        // ── Summary KPI cards ─────────────────────────────────────────────────
        $totalOmzetAllTime = Transaction::where('status', TransactionStatus::Approved)->sum('amount');
        $totalOmzetThisMonth = Transaction::where('status', TransactionStatus::Approved)
            ->whereYear('verified_at', $now->year)
            ->whereMonth('verified_at', $now->month)
            ->sum('amount');

        $totalNewAgentIncome = Transaction::where('status', TransactionStatus::Approved)
            ->where('type', TransactionType::NewAgent)
            ->sum('amount');

        $totalROIncome = Transaction::where('status', TransactionStatus::Approved)
            ->where('type', TransactionType::RepeatOrder)
            ->sum('amount');

        $totalCommissionsGenerated = Commission::sum('amount');
        $totalCommissionsPaid      = Commission::where('status', CommissionStatus::Paid)->sum('amount');
        $totalCommissionsPending   = Commission::whereIn('status', [
            CommissionStatus::Menunggu,
            CommissionStatus::Pending,
        ])->sum('amount');

        // ── 12-month income trend ────────────────────────────────────────────
        $months     = collect();
        $incomeData = collect();
        for ($i = 11; $i >= 0; $i--) {
            $m    = $now->copy()->subMonths($i);
            $val  = Transaction::where('status', TransactionStatus::Approved)
                ->whereYear('verified_at', $m->year)
                ->whereMonth('verified_at', $m->month)
                ->sum('amount');
            $months->push($m->translatedFormat('M Y'));
            $incomeData->push((float) $val);
        }

        // ── Filtered transaction table ────────────────────────────────────────
        $query = Transaction::with(['agent.user', 'adminVerifier:id,username', 'superadminVerifier:id,username'])
            ->when($filterType !== 'all', fn ($q) => $q->where('type', $filterType))
            ->when($filterStatus !== 'all', fn ($q) => $q->where('status', $filterStatus))
            ->when($filterMonth, function ($q) use ($filterMonth, $timezone) {
                try {
                    $m = Carbon::createFromFormat('Y-m', $filterMonth, $timezone);
                    $q->whereYear('created_at', $m->year)
                      ->whereMonth('created_at', $m->month);
                } catch (\Exception $e) {}
            })
            ->latest()
            ->paginate(15);

        // Retrieve calculations from session (if any)
        $calculations = session('finance_calculations');

        // Query all approved transactions in the current month (Asia/Makassar)
        $dbTransactions = Transaction::where('status', TransactionStatus::Approved)
            ->whereYear('verified_at', $now->year)
            ->whereMonth('verified_at', $now->month)
            ->get();

        $currentMonthTransactions = $dbTransactions->map(function ($txn) {
            return [
                'id' => $txn->id,
                'type' => $txn->type->value,
                'verified_at' => $txn->verified_at ? $txn->verified_at->format('Y-m-d') : null,
            ];
        });

        if (!$calculations) {
            $newAgentCount = $dbTransactions->where('type', TransactionType::NewAgent)->count();
            $roCount       = $dbTransactions->where('type', TransactionType::RepeatOrder)->count();
            $totalKeluar   = ($newAgentCount * 10) + ($roCount * 10);
            $stokAwal      = 1000;
            $sisaStok      = $stokAwal - $totalKeluar;

            $calculations = [
                'stok_awal'            => $stokAwal,
                'total_keluar'         => $totalKeluar,
                'sisa_stok'            => $sisaStok,
                'new_agent_count'      => $newAgentCount,
                'new_agent_bonus_cnt'  => 0,
                'ro_count'             => $roCount,
                'ro_bonus_cnt'         => 0,
                'bonus_new_agent'      => 0,
                'bonus_repeat_order'   => 0,
                'promo_start'          => null,
                'promo_end'            => null,
                'calculated_at'        => null,
            ];
        }

        return view('superadmin.finance.index', compact(
            'totalOmzetAllTime',
            'totalOmzetThisMonth',
            'totalNewAgentIncome',
            'totalROIncome',
            'totalCommissionsGenerated',
            'totalCommissionsPaid',
            'totalCommissionsPending',
            'months',
            'incomeData',
            'query',
            'filterType',
            'filterStatus',
            'filterMonth',
            'calculations',
            'currentMonthTransactions'
        ));
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'stok_awal'          => 'required|integer|min:0',
            'bonus_new_agent'    => 'required|integer|min:0',
            'bonus_repeat_order' => 'required|integer|min:0',
            'promo_start'        => 'nullable|date',
            'promo_end'          => 'nullable|date',
        ]);

        $stokAwal       = (int) $validated['stok_awal'];
        $bonusNewAgent  = (int) $validated['bonus_new_agent'];
        $bonusRO        = (int) $validated['bonus_repeat_order'];
        $promoStart     = $validated['promo_start'] ? Carbon::parse($validated['promo_start'])->startOfDay() : null;
        $promoEnd       = $validated['promo_end'] ? Carbon::parse($validated['promo_end'])->endOfDay() : null;

        // Query all approved transactions in the current month (Asia/Makassar)
        $timezone = 'Asia/Makassar';
        $now = Carbon::now($timezone);

        $transactions = Transaction::with('agent')
            ->where('status', TransactionStatus::Approved)
            ->whereYear('verified_at', $now->year)
            ->whereMonth('verified_at', $now->month)
            ->get();

        $totalKeluar = 0;
        $newAgentCount = 0;
        $newAgentBonusCount = 0;
        $roCount = 0;
        $roBonusCount = 0;

        foreach ($transactions as $txn) {
            $verifiedAt = Carbon::parse($txn->verified_at);
            $isPromoActive = false;

            if ($promoStart && $promoEnd) {
                $isPromoActive = $verifiedAt->between($promoStart, $promoEnd);
            }

            if ($txn->type === TransactionType::NewAgent) {
                $newAgentCount++;
                $qty = 10; // Default 10 botol
                if ($isPromoActive) {
                    $qty += $bonusNewAgent;
                    $newAgentBonusCount++;
                }
                $totalKeluar += $qty;
            } elseif ($txn->type === TransactionType::RepeatOrder) {
                $roCount++;
                $qty = 10; // Default 10 botol
                if ($isPromoActive) {
                    $qty += $bonusRO;
                    $roBonusCount++;
                }
                $totalKeluar += $qty;
            }
        }

        $sisaStok = $stokAwal - $totalKeluar;

        $results = [
            'stok_awal'            => $stokAwal,
            'total_keluar'         => $totalKeluar,
            'sisa_stok'            => $sisaStok,
            'new_agent_count'      => $newAgentCount,
            'new_agent_bonus_cnt'  => $newAgentBonusCount,
            'ro_count'             => $roCount,
            'ro_bonus_cnt'         => $roBonusCount,
            'bonus_new_agent'      => $bonusNewAgent,
            'bonus_repeat_order'   => $bonusRO,
            'promo_start'          => $validated['promo_start'],
            'promo_end'            => $validated['promo_end'],
            'calculated_at'        => now()->format('d/m/Y H:i'),
        ];

        return redirect()->route('superadmin.finance.index')
            ->with('finance_calculations', $results)
            ->with('active_tab', 'calculator');
    }

    public function downloadPdf(Request $request)
    {
        $validated = $request->validate([
            'stok_awal'          => 'required|integer',
            'total_keluar'         => 'required|integer',
            'sisa_stok'            => 'required|integer',
            'new_agent_count'      => 'required|integer',
            'new_agent_bonus_cnt'  => 'required|integer',
            'ro_count'             => 'required|integer',
            'ro_bonus_cnt'         => 'required|integer',
            'bonus_new_agent'      => 'required|integer',
            'bonus_repeat_order'   => 'required|integer',
            'promo_start'          => 'nullable|date',
            'promo_end'            => 'nullable|date',
        ]);

        $pdf = Pdf::loadView('superadmin.finance.pdf', [
            'data' => $validated,
            'date' => now()->format('d F Y H:i')
        ]);

        return $pdf->download('Laporan_Keuangan_Stok_SahihStore_' . now()->format('Ymd') . '.pdf');
    }
}
