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
        try {
            $superadmin = $request->user();

            // Lock the transaction row FIRST so concurrent requests cannot
            // both pass the status guard and trigger duplicate commissions.
            DB::beginTransaction();

            /** @var Transaction $tx */
            $tx = Transaction::where('id', $transaction->id)->lockForUpdate()->firstOrFail();

            // Guard: re-read status from the locked row, not the route-bound model.
            if (! in_array($tx->status, [TransactionStatus::PendingSuperadmin, TransactionStatus::Pending])) {
                DB::rollBack();
                return back()->with('error', 'Transaksi ini tidak dalam status yang dapat disetujui.');
            }

            if ($tx->type === TransactionType::NewAgent) {
                $this->registrationService->approveRegistration($tx, $superadmin);
                $message = "Registrasi Agen {$tx->agent->nama} disetujui. Komisi telah dibagikan.";
            } elseif ($tx->type === TransactionType::RepeatOrder) {
                $this->repeatOrderService->approveOrder($tx, $superadmin);
                $message = "Repeat Order dari {$tx->agent->nama} disetujui. Poin & komisi telah dibagikan.";
            } else {
                throw new \Exception("Tipe transaksi tidak dikenali.");
            }

            // NOTE: verified_by_superadmin_id and verified_at are already set by
            // the service (approveRegistration / approveOrder). No duplicate update needed here.

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
        try {
            $superadmin = $request->user();

            DB::beginTransaction();

            /** @var Transaction $tx */
            $tx = Transaction::where('id', $transaction->id)->lockForUpdate()->firstOrFail();

            if ($tx->status === TransactionStatus::Approved) {
                DB::rollBack();
                return back()->with('error', 'Transaksi yang sudah disetujui tidak dapat ditolak.');
            }

            if ($tx->type === TransactionType::NewAgent) {
                $this->registrationService->rejectRegistration($tx, $superadmin);
            } elseif ($tx->type === TransactionType::RepeatOrder) {
                $this->repeatOrderService->rejectOrder($tx, $superadmin);
            }

            // NOTE: verified_by_superadmin_id and verified_at already set by service.

            DB::commit();
            return back()->with('success', "Transaksi #{$tx->id} berhasil ditolak.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
