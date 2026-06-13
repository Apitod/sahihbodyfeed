<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Transaction;
use App\Services\AgentRegistrationService;
use App\Services\CommissionDistributionService;
use App\Services\RepeatOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    public function __construct(
        private readonly AgentRegistrationService $registrationService,
        private readonly RepeatOrderService $repeatOrderService,
        private readonly CommissionDistributionService $commissionService
    ) {}

    public function transactionsList(Request $request)
    {
        $statusFilter = $request->get('status', 'all');

        $transactions = Transaction::with(['agent.user', 'verifier'])
            ->where(function ($q) {
                $q->where('type', '!=', TransactionType::NewAgent)
                    ->orWhere('amount', '>', 0);
            })
            ->when($statusFilter !== 'all', fn ($q) => $q->where('status', $statusFilter))
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 WHEN status = 'pending_superadmin' THEN 2 ELSE 3 END")
            ->latest()
            ->paginate(50);

        $pendingCount = Transaction::where('status', TransactionStatus::Pending)->count();

        return view('admin.verifications.transactions', compact('transactions', 'statusFilter', 'pendingCount'));
    }

    /**
     * Show form for admin to create a Repeat Order on behalf of an agent.
     * Agent lookup is done by username.
     */
    public function createRepeatOrder(Request $request)
    {
        $amount = \App\Enums\TransactionType::RepeatOrder->amount();

        // If a username is submitted, resolve the agent for preview
        $agent = null;
        $hasApprovedVerification = false;
        if ($username = $request->input('username')) {
            $agent = Agent::whereHas('user', fn ($q) => $q->where('username', $username))
                ->with('user')
                ->first();

            if ($agent) {
                $hasApprovedVerification = $agent->transactions()
                    ->where('type', TransactionType::NewAgent)
                    ->where('status', TransactionStatus::Approved)
                    ->exists();
            }
        }

        return view('admin.verifications.create_ro', compact('amount', 'agent', 'username', 'hasApprovedVerification'));
    }

    public function createAgentVerification(Request $request)
    {
        $amount = TransactionType::NewAgent->amount();
        $agent = null;
        $hasApprovedVerification = false;
        $hasPendingVerification = false;

        if ($username = $request->input('username')) {
            $agent = Agent::whereHas('user', fn ($q) => $q->where('username', $username))
                ->with('user')
                ->first();

            if ($agent) {
                $hasApprovedVerification = $agent->transactions()
                    ->where('type', TransactionType::NewAgent)
                    ->where('status', TransactionStatus::Approved)
                    ->exists();

                $hasPendingVerification = $agent->transactions()
                    ->where('type', TransactionType::NewAgent)
                    ->whereIn('status', [TransactionStatus::Pending, TransactionStatus::PendingSuperadmin])
                    ->exists();
            }
        }

        return view('admin.verifications.verify_agent', compact('amount', 'agent', 'username', 'hasApprovedVerification', 'hasPendingVerification'));
    }

    public function storeAgentVerification(Request $request)
    {
        $validated = $request->validate([
            'username'         => 'required|string|exists:users,username',
            'payment_option'   => 'required|in:bayar,free',
            'proof_of_payment' => 'required_if:payment_option,bayar|nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        $agent = Agent::whereHas('user', fn ($q) => $q->where('username', $validated['username']))
            ->with('user')
            ->first();

        if (! $agent) {
            return back()->withErrors(['username' => 'Agen dengan username tersebut tidak ditemukan.'])->withInput();
        }

        $hasExistingVerification = $agent->transactions()
            ->where('type', TransactionType::NewAgent)
            ->whereIn('status', [TransactionStatus::Pending, TransactionStatus::PendingSuperadmin, TransactionStatus::Approved])
            ->exists();

        if ($hasExistingVerification) {
            return back()->withErrors(['username' => 'Agen ini sudah memiliki proses/verifikasi agent.'])->withInput();
        }

        $isFree = $validated['payment_option'] === 'free';
        $path = $request->hasFile('proof_of_payment')
            ? $request->file('proof_of_payment')->store('payments', 'public')
            : null;

        DB::transaction(function () use ($agent, $path, $isFree, $request) {
            $transaction = Transaction::create([
                'agent_id'              => $agent->id,
                'type'                  => TransactionType::NewAgent,
                'amount'                => $isFree ? 0 : TransactionType::NewAgent->amount(),
                'status'                => TransactionStatus::Approved,
                'proof_of_payment'      => $path,
                'verified_by_admin_id'  => $request->user()->id,
                'verified_at'           => now(),
            ]);

            $agent->update(['joined_at' => $agent->joined_at ?? now()]);
            $agent->user?->update(['is_active' => true]);

            if ($isFree) {
                Log::info("VerifikasiAgent: Free approved — Agent[{$agent->id}] Transaction[{$transaction->id}] amount Rp0. No commissions distributed.");
            } else {
                $commissions = $this->commissionService->distribute($transaction);
                Log::info("VerifikasiAgent: Paid approved — Agent[{$agent->id}] Transaction[{$transaction->id}] amount Rp" . number_format(TransactionType::NewAgent->amount()) . '. ' . count($commissions) . ' commission(s) distributed.');
            }
        });

        $message = $isFree
            ? "Verifikasi Agent Free untuk '{$agent->nama}' berhasil disetujui tanpa komisi."
            : "Verifikasi Agent berbayar untuk '{$agent->nama}' berhasil disetujui dan komisi dibagikan.";

        return redirect()->route('admin.verifications.transactions')
            ->with('success', $message);
    }

    /**
     * Admin submits a Repeat Order transaction on behalf of an agent (found by username).
     */
    public function storeRepeatOrder(Request $request)
    {
        $validated = $request->validate([
            'username'         => 'required|string|exists:users,username',
            'proof_of_payment' => 'nullable|image|max:2048',
        ]);

        $agent = Agent::whereHas('user', fn ($q) => $q->where('username', $validated['username']))
            ->with('user')
            ->first();

        if (! $agent) {
            return back()->withErrors(['username' => 'Agen dengan username tersebut tidak ditemukan.'])->withInput();
        }

        if (! $agent->user?->is_active) {
            return back()->withErrors(['username' => 'Akun agen ini tidak aktif.'])->withInput();
        }

        $hasApprovedVerification = $agent->transactions()
            ->where('type', TransactionType::NewAgent)
            ->where('status', TransactionStatus::Approved)
            ->exists();

        if (! $hasApprovedVerification) {
            return back()->withErrors(['username' => 'Agen belum Verifikasi Agent, tidak bisa melakukan Repeat Order.'])->withInput();
        }

        if($request->hasFile('proof_of_payment')) {
            $path = $request->file('proof_of_payment')->store('payments', 'public');
        } else {
            $path = '';
        }

        $this->repeatOrderService->submitOrder($agent, $path);

        return redirect()->route('admin.verifications.transactions')
            ->with('success', "Repeat Order untuk agen '{$agent->nama}' (@{$agent->user->username}) berhasil dibuat.");
    }

    /**
     * Admin Tier-2: first-level review.
     * Only moves the transaction to 'pending_superadmin'.
     * Commission distribution happens only after Superadmin gives final approval.
     */
    public function approveTransaction(Transaction $transaction, Request $request)
    {
        if ($transaction->status !== \App\Enums\TransactionStatus::Pending) {
            return back()->with('error', 'Hanya transaksi berstatus Pending yang dapat direview.');
        }

        $transaction->update([
            'status'                 => \App\Enums\TransactionStatus::PendingSuperadmin,
            'verified_by_admin_id'  => $request->user()->id,
        ]);

        return back()->with('success', "Transaksi #{$transaction->id} telah diverifikasi. Menunggu persetujuan akhir Superadmin.");
    }

    /**
     * Admin Tier-2: reject directly (no need to escalate to Superadmin for rejections).
     */
    public function rejectTransaction(Transaction $transaction, Request $request)
    {
        if ($transaction->status !== \App\Enums\TransactionStatus::Pending) {
            return back()->with('error', 'Hanya transaksi berstatus Pending yang dapat ditolak oleh Admin.');
        }

        // Admin can reject directly without superadmin — it goes straight to Rejected.
        if ($transaction->type === TransactionType::NewAgent) {
            $this->registrationService->rejectRegistration($transaction, $request->user());
        } elseif ($transaction->type === TransactionType::RepeatOrder) {
            $this->repeatOrderService->rejectOrder($transaction, $request->user());
        }

        return back()->with('success', "Transaksi #{$transaction->id} berhasil ditolak.");
    }
}
