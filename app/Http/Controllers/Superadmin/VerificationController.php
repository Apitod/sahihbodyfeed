<?php

namespace App\Http\Controllers\Superadmin;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\AgentRegistrationService;
use App\Services\RepeatOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Superadmin Tier-1 Verification Controller
 *
 * Superadmin ONLY acts on transactions that have already been
 * reviewed by an Admin (status = pending_superadmin).
 * Superadmin can also directly override / reject any transaction.
 */
class VerificationController extends Controller
{
    public function __construct(
        private readonly AgentRegistrationService $registrationService,
        private readonly RepeatOrderService $repeatOrderService
    ) {}

    /**
     * List transactions pending Superadmin final approval.
     */
    public function transactionsList(Request $request)
    {
        $statusFilter = $request->get('status', 'pending_superadmin');

        $transactions = Transaction::with(['agent.user', 'adminVerifier:id,username', 'superadminVerifier:id,username'])
            ->when($statusFilter !== 'all', fn ($q) => $q->where('status', $statusFilter))
            ->latest()
            ->paginate(50);

        $pendingCount = Transaction::where('status', TransactionStatus::PendingSuperadmin)->count();

        return view('superadmin.verifications.transactions', compact('transactions', 'statusFilter', 'pendingCount'));
    }

    /**
     * FINAL APPROVE: Superadmin confirms the transaction.
     * This triggers commission distribution / agent activation.
     */
    public function approveTransaction(Transaction $transaction, Request $request)
    {
        // Guard: only proceed if Admin has already reviewed (or superadmin bypasses)
        if (! in_array($transaction->status, [TransactionStatus::PendingSuperadmin, TransactionStatus::Pending])) {
            return back()->with('error', 'Transaksi ini tidak dalam status yang dapat disetujui.');
        }

        try {
            $superadmin = $request->user();

            DB::beginTransaction();

            if ($transaction->type === TransactionType::NewAgent) {
                $this->registrationService->approveRegistration($transaction, $superadmin);
                $message = "Registrasi Agen {$transaction->agent->nama} disetujui. Komisi telah dibagikan.";
            } elseif ($transaction->type === TransactionType::RepeatOrder) {
                $this->repeatOrderService->approveOrder($transaction, $superadmin);
                $message = "Repeat Order dari {$transaction->agent->nama} disetujui. Poin & komisi telah dibagikan.";
            } else {
                throw new \Exception("Tipe transaksi tidak dikenali.");
            }

            // Record the superadmin who gave final approval
            $transaction->update([
                'verified_by_superadmin_id' => $superadmin->id,
                'verified_at'               => now(),
            ]);

            DB::commit();
            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * FINAL REJECT: Superadmin rejects the transaction.
     */
    public function rejectTransaction(Transaction $transaction, Request $request)
    {
        if ($transaction->status === TransactionStatus::Approved) {
            return back()->with('error', 'Transaksi yang sudah disetujui tidak dapat ditolak.');
        }

        try {
            $superadmin = $request->user();

            DB::beginTransaction();

            if ($transaction->type === TransactionType::NewAgent) {
                $this->registrationService->rejectRegistration($transaction, $superadmin);
            } elseif ($transaction->type === TransactionType::RepeatOrder) {
                $this->repeatOrderService->rejectOrder($transaction, $superadmin);
            }

            $transaction->update([
                'verified_by_superadmin_id' => $superadmin->id,
                'verified_at'               => now(),
            ]);

            DB::commit();
            return back()->with('success', "Transaksi #{$transaction->id} berhasil ditolak.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
