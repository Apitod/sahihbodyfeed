<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Enums\TransactionStatus;
use App\Enums\ClaimStatus;
use App\Models\Agent;
use App\Models\Commission;
use App\Models\RewardClaim;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now            = Carbon::now();
        $startOfMonth   = $now->copy()->startOfMonth();
        $endOfMonth     = $now->copy()->endOfMonth();
        $driver         = DB::getDriverName();

        // Helper date expressions — cross-driver (PostgreSQL, MySQL, SQLite)
        if ($driver === 'sqlite') {
            $monthExpr = "CAST(strftime('%m', created_at) AS INTEGER)";
            $yearExpr  = "strftime('%Y', created_at)";
        } elseif ($driver === 'pgsql') {
            $monthExpr = 'CAST(EXTRACT(MONTH FROM created_at) AS INTEGER)';
            $yearExpr  = 'CAST(EXTRACT(YEAR FROM created_at) AS INTEGER)';
        } else {
            // MySQL / MariaDB
            $monthExpr = 'MONTH(created_at)';
            $yearExpr  = 'YEAR(created_at)';
        }

        // ─── 1. Pantauan Penjualan ────────────────────────────────────────────────

        $totalSales = Transaction::where('status', TransactionStatus::Approved)->sum('amount');

        $monthlySales = Transaction::where('status', TransactionStatus::Approved)
            ->whereBetween('verified_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // ─── 2. Grafik RO Bulanan (12 bulan tahun ini) ───────────────────────────

        $roByMonth = Transaction::where('type', 'repeat_order')
            ->where('status', TransactionStatus::Approved)
            ->whereRaw("{$yearExpr} = ?", [$now->year])
            ->selectRaw("{$monthExpr} as month, COUNT(id) as count_ro, SUM(amount) as total_ro")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Fill semua 12 bulan (untuk grafik bar/line yang lengkap)
        $roChartData = collect(range(1, 12))->map(fn ($m) => [
            'month'    => $m,
            'count_ro' => $roByMonth->get($m)?->count_ro ?? 0,
            'total_ro' => $roByMonth->get($m)?->total_ro ?? 0,
        ]);

        // ─── 3. Komisi ────────────────────────────────────────────────────────────

        $totalCommissions = Commission::sum('amount');
        $paidCommissions  = Commission::where('status', 'paid')->sum('amount');
        $unpaidCommissions = $totalCommissions - $paidCommissions;

        // ─── 4. Statistik Klaim Reward ────────────────────────────────────────────

        $rewardStats = RewardClaim::selectRaw('status, COUNT(id) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $pendingClaimsCount           = $rewardStats->get(ClaimStatus::Pending->value, 0);
        $pendingSuperadminClaimsCount = $rewardStats->get(ClaimStatus::PendingSuperadmin->value, 0);
        $approvedClaimsCount          = $rewardStats->get(ClaimStatus::Approved->value, 0);

        // ─── 5. Pertumbuhan Agen Baru ─────────────────────────────────────────────

        if ($driver === 'sqlite') {
            $agentGrowthExpr     = "CAST(strftime('%m', joined_at) AS INTEGER)";
            $agentGrowthYearExpr = "strftime('%Y', joined_at)";
        } elseif ($driver === 'pgsql') {
            $agentGrowthExpr     = 'CAST(EXTRACT(MONTH FROM joined_at) AS INTEGER)';
            $agentGrowthYearExpr = 'CAST(EXTRACT(YEAR FROM joined_at) AS INTEGER)';
        } else {
            $agentGrowthExpr     = 'MONTH(joined_at)';
            $agentGrowthYearExpr = 'YEAR(joined_at)';
        }

        $agentGrowthByMonth = Agent::whereRaw("{$agentGrowthYearExpr} = ?", [$now->year])
            ->selectRaw("{$agentGrowthExpr} as month, COUNT(id) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $agentChartData = collect(range(1, 12))->map(fn ($m) => [
            'month' => $m,
            'total' => $agentGrowthByMonth->get($m)?->total ?? 0,
        ]);

        $newAgentsThisMonth = $agentGrowthByMonth->get($now->month)?->total ?? 0;
        $totalAgents        = Agent::count();

        // ─── 6. Monitoring Kinerja Admin ─────────────────────────────────────────

        // Admin Tier-2: hitung berapa transaksi yang mereka proses bulan ini
        $adminPerformance = Transaction::whereNotNull('verified_by_admin_id')
            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('verified_by_admin_id, COUNT(id) as total_verified')
            ->groupBy('verified_by_admin_id')
            ->with('adminVerifier:id,username,role')
            ->orderByDesc('total_verified')
            ->get();

        // Queue: pengajuan yang masih menunggu tindakan
        $pendingForAdmin       = Transaction::where('status', TransactionStatus::Pending)->count();
        $pendingForSuperadmin  = Transaction::where('status', TransactionStatus::PendingSuperadmin)->count();

        // Transaksi terbaru yang perlu perhatian Superadmin
        $pendingSuperadminTx = Transaction::with(['agent', 'adminVerifier:id,username'])
            ->where('status', TransactionStatus::PendingSuperadmin)
            ->latest()
            ->limit(10)
            ->get();

        return view('superadmin.dashboard', compact(
            'totalSales',
            'monthlySales',
            'roChartData',
            'totalCommissions',
            'paidCommissions',
            'unpaidCommissions',
            'pendingClaimsCount',
            'pendingSuperadminClaimsCount',
            'approvedClaimsCount',
            'totalAgents',
            'newAgentsThisMonth',
            'agentChartData',
            'adminPerformance',
            'pendingForAdmin',
            'pendingForSuperadmin',
            'pendingSuperadminTx'
        ));
    }
}
