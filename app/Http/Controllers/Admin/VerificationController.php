<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\AgentRegistrationService;
use App\Services\RepeatOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerificationController extends Controller
{
    public function __construct(
        private readonly AgentRegistrationService $registrationService,
        private readonly RepeatOrderService $repeatOrderService
    ) {}

    public function transactionsList(Request $request)
    {
        $statusFilter = $request->get('status', 'all');

        $transactions = Transaction::with(['agent.user', 'verifier'])
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
        if ($username = $request->input('username')) {
            $agent = \App\Models\Agent::whereHas('user', fn ($q) => $q->where('username', $username))
                ->with('user')
                ->first();
        }

        return view('admin.verifications.create_ro', compact('amount', 'agent', 'username'));
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

        $agent = \App\Models\Agent::whereHas('user', fn ($q) => $q->where('username', $validated['username']))
            ->with('user')
            ->first();

        if (! $agent) {
            return back()->withErrors(['username' => 'Agen dengan username tersebut tidak ditemukan.'])->withInput();
        }

        if (! $agent->user?->is_active) {
            return back()->withErrors(['username' => 'Akun agen ini tidak aktif.'])->withInput();
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
