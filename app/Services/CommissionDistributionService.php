<?php

namespace App\Services;

use App\Enums\CommissionStatus;
use App\Enums\CommissionType;
use App\Enums\TransactionType;
use App\Models\Agent;
use App\Models\Commission;
use App\Models\Transaction;
use App\Notifications\CommissionReceivedNotification;
use Illuminate\Support\Facades\Log;

/**
 * CommissionDistributionService
 *
 * Handles all upline commission calculations triggered after a transaction
 * is verified by an admin. Called internally by AgentRegistrationService
 * and RepeatOrderService after they mark a transaction as 'verified'.
 *
 * This is a stateless service — it reads from the DB and writes Commission
 * rows. All callers must wrap their outer DB::transaction() to ensure atomicity.
 *
 * FC1 — new_agent:
 *   Walks up to 3 upline generations from the new agent.
 *   Gen-1 (direct sponsor)   → Rp450,000
 *   Gen-2 (sponsor's upline) → Rp100,000
 *   Gen-3 (Gen-2's upline)   → Rp100,000
 *
 * FC2 — repeat_order:
 *   Only the direct sponsor receives a commission.
 *   Gen-1 → Rp250,000
 */
class CommissionDistributionService
{
    /**
     * Entry point. Distributes commissions for a verified transaction.
     *
     * @param Transaction $transaction A transaction with status = 'verified'.
     * @return Commission[]            All Commission rows created.
     */
    public function distribute(Transaction $transaction): array
    {
        $commissionType = CommissionType::fromTransactionType($transaction->type);
        $commissions    = [];

        // Start the upline walk from the transaction's agent.
        // For new_agent: the new agent's direct sponsor is Gen-1.
        // For repeat_order: the ordering agent's direct sponsor is Gen-1.
        $currentAgent = $transaction->agent()->with('upline')->first();
        $upline       = $currentAgent?->upline;

        for ($generation = 1; $generation <= $commissionType->maxGenerations(); $generation++) {
            if ($upline === null) {
                // No more uplines in the chain — stop walking.
                Log::info("CommissionDistribution: upline chain ended at generation {$generation} for transaction #{$transaction->id}.");
                break;
            }

            $amount = $commissionType->amountForGeneration($generation);

            if ($amount > 0) {
                $commission = Commission::create([
                    'transaction_id'   => $transaction->id,
                    'recipient_id'     => $upline->id,
                    'amount'           => $amount,
                    'generation_level' => $generation,
                    'type'             => $commissionType,
                    'status'           => CommissionStatus::Menunggu, // Waiting — cron will process at 01:00 WITA.
                    'paid_at'          => null,                        // Set only after admin confirms disbursement.
                ]);

                $commissions[] = $commission;

                Log::info("CommissionDistribution: Created commission #{$commission->id} — Gen-{$generation} recipient Agent[{$upline->id}] amount Rp" . number_format($amount) . " for transaction #{$transaction->id}.");

                // Notify the recipient agent.
                if ($upline->user) {
                    $upline->user->notify(new CommissionReceivedNotification($commission));
                }

                /*
                 * === DISBURSEMENT HOOK ===
                 * In production, trigger the actual bank transfer / e-wallet credit here.
                 * Example:
                 *   $this->payoutGateway->transfer($upline, $amount);
                 * For now, the DB record is the source of truth.
                 */
            }

            // Move up one generation for next iteration.
            $upline = $upline->upline;
        }

        return $commissions;
    }
}
