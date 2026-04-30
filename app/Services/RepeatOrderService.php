<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Exceptions\InvalidTransactionStateException;
use App\Models\Agent;
use App\Models\Transaction;
use App\Models\User;
use App\Enums\UserRole;
use App\Notifications\NewTransactionNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * RepeatOrderService
 *
 * Implements Flowchart 2 — "Repeat Order".
 *
 * FLOW:
 *   1. submitOrder() — A registered, active agent submits a repeat product order
 *      (Rp2,350,000) with proof of payment. Creates a PENDING transaction.
 *
 *   2. approveOrder() — Admin verifies payment.
 *      Two parallel actions fire:
 *        (a) CommissionDistributionService: direct sponsor receives Rp250,000.
 *        (b) Agent's total_points incremented by +1.
 *
 *   3. rejectOrder() — Admin rejects. No commission, no point increment.
 *
 * NOTE: Unlike FC1, the agent already exists and is active. No user/agent
 * creation occurs here — only transaction management.
 */
class RepeatOrderService
{
    public function __construct(
        private readonly CommissionDistributionService $commissionService,
    ) {}

    /**
     * Step 1 — Registered agent submits a repeat order.
     *
     * @param Agent  $agent           The registered agent placing the order.
     * @param string $proofOfPayment  File path/name of the payment proof upload.
     * @return Transaction            The newly created pending transaction.
     */
    public function submitOrder(Agent $agent, string $proofOfPayment): Transaction
    {
        return DB::transaction(function () use ($agent, $proofOfPayment) {
            $transaction = Transaction::create([
                'agent_id'         => $agent->id,
                'type'             => TransactionType::RepeatOrder,
                'amount'           => TransactionType::RepeatOrder->amount(),
                'status'           => TransactionStatus::Pending,
                'proof_of_payment' => $proofOfPayment,
            ]);

            Log::info("RepeatOrder: Submitted — Agent[{$agent->id}] Transaction[{$transaction->id}].");

            // Notify all admins.
            $admins = User::where('role', UserRole::Admin)->get();
            Notification::send($admins, new NewTransactionNotification($transaction));

            return $transaction;
        });
    }

    /**
     * Step 2 — Admin approves the repeat order (Admin Verifikasi Pembayaran → Distribusi).
     *
     * FC2 path triggers TWO parallel actions after approval:
     *   LEFT  branch: sponsor receives commission Rp250,000.
     *   RIGHT branch: ordering agent gets total_points + 1.
     *
     * @param Transaction $transaction The pending repeat_order transaction to approve.
     * @param User        $adminUser   The admin performing the verification.
     * @throws InvalidTransactionStateException
     */
    public function approveOrder(Transaction $transaction, User $adminUser): void
    {
        if ($transaction->type !== TransactionType::RepeatOrder) {
            throw new InvalidTransactionStateException(
                $transaction,
                "approveOrder() hanya untuk transaksi bertipe 'repeat_order'. Tipe diterima: {$transaction->type->value}."
            );
        }

        if ($transaction->status !== TransactionStatus::Pending) {
            throw new InvalidTransactionStateException($transaction);
        }

        DB::transaction(function () use ($transaction, $adminUser) {
            // 1. Mark transaction as verified.
            $transaction->update([
                'status'      => TransactionStatus::Verified,
                'verified_by' => $adminUser->id,
                'verified_at' => now(),
            ]);

            $agent = $transaction->agent()->firstOrFail();

            // 2a. LEFT BRANCH (FC2): Distribute commission to direct sponsor (Rp250,000).
            $commissions = $this->commissionService->distribute($transaction);

            // 2b. RIGHT BRANCH (FC2): Increment the ordering agent's total_points by +1.
            // NOTE: We use increment() for atomicity — avoids race conditions with concurrent orders.
            $agent->increment('total_points');

            Log::info(
                "RepeatOrder: Approved — Agent[{$agent->id}] points now [{$agent->fresh()->total_points}]. " .
                count($commissions) . " commission(s) distributed."
            );
        });
    }

    /**
     * Step 3 — Admin rejects the repeat order.
     * No commission is distributed and no points are awarded.
     *
     * @param Transaction $transaction The pending repeat_order transaction to reject.
     * @param User        $adminUser   The admin performing the rejection.
     */
    public function rejectOrder(Transaction $transaction, User $adminUser): void
    {
        if ($transaction->type !== TransactionType::RepeatOrder) {
            throw new InvalidTransactionStateException(
                $transaction,
                "rejectOrder() hanya untuk transaksi bertipe 'repeat_order'."
            );
        }

        if ($transaction->status !== TransactionStatus::Pending) {
            throw new InvalidTransactionStateException($transaction);
        }

        DB::transaction(function () use ($transaction, $adminUser) {
            $transaction->update([
                'status'      => TransactionStatus::Rejected,
                'verified_by' => $adminUser->id,
                'verified_at' => now(),
            ]);

            Log::warning("RepeatOrder: Rejected — Transaction[{$transaction->id}] by Admin[{$adminUser->id}].");
        });
    }
}
