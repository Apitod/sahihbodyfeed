<?php

namespace App\Services;

use App\Enums\ClaimStatus;
use App\Enums\MatchingRewardStatus;
use App\Exceptions\InsufficientPointsException;
use App\Models\Agent;
use App\Models\MatchingRewardLog;
use App\Models\Reward;
use App\Models\RewardClaim;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * RewardClaimService
 *
 * Implements Flowchart 3 — "Klaim Reward & Matching Reward".
 *
 * ═══════════════════════════════════════════════════════════════════
 * FULL FLOWCHART 3 LOGIC (read carefully before modifying):
 * ═══════════════════════════════════════════════════════════════════
 *
 *  [START] Agent mengajukan klaim reward
 *     ↓
 *  [DECISION] Poin Agen Cukup?
 *     NO  → Error: Poin Tidak Cukup → [END] (throws InsufficientPointsException)
 *     YES ↓
 *  [ACTION] Akumulasi Poin tetap & Tandai Reward as Claimed. Cairkan reward_value ke Agen.
 *  [ACTION] Update Status Level Agen Otomatis (via AgentStatus::fromPoints())
 *     ↓
 *  ╔══ KASUS B TRIGGER ══════════════════════════════════════════════╗
 *  ║ Before checking upline, check if THIS AGENT is a sponsor for   ║
 *  ║ any PENDING matching_reward_logs for this same reward_id.      ║
 *  ║ (This agent's own claim approval fires deferred payouts.)      ║
 *  ╚═════════════════════════════════════════════════════════════════╝
 *     ↓
 *  [PROCESS] Sistem Mencari Siapa Sponsor Langsung Agen ini?
 *     NO UPLINE → Skip matching reward → [END]
 *     ↓
 *  [DECISION] Sponsor Sudah Pernah Klaim Reward yang Sama?
 *     YES (Kasus A) → Cairkan Matching Reward 100% (reward_value) ke Sponsor immediately.
 *                     Create MatchingRewardLog { status: paid, paid_at: now() }
 *     NO  (Kasus B) → Simpan ke matching_reward_logs { status: pending }
 *                     Trigger fires later when sponsor approves their own same reward.
 *
 * ═══════════════════════════════════════════════════════════════════
 * submitClaim() vs approveClaim() decision:
 *
 * The ERD has approved_by on reward_claims, suggesting admin oversight.
 * We use a two-step approach:
 *   - submitClaim()   : Validates points immediately. Creates 'pending' record.
 *                       If points insufficient, throws immediately (FC3 error path).
 *   - approveClaim()  : Admin confirms → executes the full FC3 disbursement logic.
 *
 * This gives the admin a final confirmation gate before cash is disbursed,
 * while still surfacing point errors immediately on submission.
 * ═══════════════════════════════════════════════════════════════════
 */
class RewardClaimService
{
    /**
     * Step 1 — Agent submits a reward claim.
     *
     * Validates total_points >= reward.required_points immediately.
     * If insufficient, throws InsufficientPointsException (FC3 terminal error state).
     * If sufficient, creates a pending RewardClaim record for admin review.
     *
     * @throws InsufficientPointsException
     */
    public function submitClaim(Agent $agent, Reward $reward): RewardClaim
    {
        // [DECISION] Poin Agen Cukup?
        if ($agent->total_points < $reward->required_points) {
            // NO branch → Error: Poin Tidak Cukup
            throw new InsufficientPointsException($agent, $reward);
        }

        return DB::transaction(function () use ($agent, $reward) {
            $claim = RewardClaim::create([
                'agent_id'  => $agent->id,
                'reward_id' => $reward->id,
                'status'    => ClaimStatus::Pending,
            ]);

            Log::info("RewardClaim: Submitted — Agent[{$agent->id}] Reward[{$reward->id}] Claim[{$claim->id}] ({$reward->name}).");

            return $claim;
        });
    }

    /**
     * Step 2 — Admin approves the reward claim.
     *
     * Executes the full FC3 YES branch:
     *   1. Marks claim as approved.
     *   2. Disburses reward_value to the agent (logged/marked).
     *   3. Auto-updates agent status level.
     *   4. Fires KC Kasus B trigger: checks if THIS agent is a sponsor
     *      with PENDING matching rewards for this reward → flushes them.
     *   5. Finds the agent's direct sponsor.
     *   6. Determines Kasus A or Kasus B for the sponsor's matching reward.
     *
     * @param RewardClaim $claim     The pending claim to approve.
     * @param User        $adminUser The admin confirming the claim.
     * @throws \InvalidArgumentException If claim is not in 'pending' state.
     */
    public function approveClaim(RewardClaim $claim, User $adminUser): void
    {
        if (! in_array($claim->status, [ClaimStatus::Pending, ClaimStatus::PendingSuperadmin])) {
            throw new \InvalidArgumentException(
                "Claim[{$claim->id}] sudah dalam status '{$claim->status->value}', tidak bisa di-approve ulang."
            );
        }

        DB::transaction(function () use ($claim, $adminUser) {
            // Load relationships needed throughout this method.
            $claim->load(['agent.upline', 'reward']);
            $agent  = $claim->agent;
            $reward = $claim->reward;

            // ── STEP 1: Mark claim as approved ──────────────────────────────
            $claim->update([
                'status'                    => ClaimStatus::Approved,
                'approved_by_superadmin_id' => $adminUser->id,
                'approved_at'               => now(),
            ]);

            Log::info("RewardClaim: Approved — Claim[{$claim->id}] Agent[{$agent->id}] Reward[{$reward->id}] ({$reward->name}) by Admin[{$adminUser->id}].");

            // ── STEP 2: Disburse reward_value to agent ───────────────────────
            // The agent receives reward.reward_value (e.g., Rp5,000,000).
            // Points are NOT deducted — FC3 says "Akumulasi Poin tetap" (points accumulate, not consumed).
            Log::info("RewardClaim: Disbursing Rp" . number_format((float) $reward->reward_value) . " to Agent[{$agent->id}].");
            /*
             * === DISBURSEMENT HOOK ===
             * Connect your bank transfer / e-wallet credit here.
             *   $this->payoutGateway->transfer($agent->user, $reward->reward_value);
             */

            // ── STEP 3: Auto-update agent status ────────────────────────────
            // FC3: "Update Status Level Agen Otomatis"
            // Refresh to get latest total_points in case a concurrent RO approval incremented it.
            $agent->refresh();
            $newStatus = $agent->resolveStatus(); // Delegates to AgentStatus::fromPoints()
            $agent->update(['status' => $newStatus]);
            Log::info("RewardClaim: Agent[{$agent->id}] status updated to '{$newStatus->value}'.");

            // ── STEP 4: KASUS B TRIGGER ──────────────────────────────────────
            // This agent may themselves be a sponsor who has PENDING matching
            // reward entries (created when their downlines claimed this same reward
            // before this agent had claimed it — see Kasus B explanation above).
            // Now that THIS agent has claimed, flush all their deferred payouts.
            $this->flushPendingMatchingRewards($agent, $reward);

            // ── STEP 5: Find direct sponsor ──────────────────────────────────
            // FC3: "Sistem Mencari Siapa Sponsor Langsung Agen ini?"
            $sponsor = $agent->upline;

            if ($sponsor === null) {
                // Top-level agent has no sponsor — no matching reward applies.
                Log::info("RewardClaim: Agent[{$agent->id}] has no upline. Matching reward skipped.");
                return;
            }

            // ── STEP 6: KASUS A vs KASUS B ───────────────────────────────────
            // FC3: "Sponsor Sudah Pernah Klaim Reward yang Sama?"
            if ($sponsor->hasApprovedClaimForReward($reward->id)) {

                // ══ KASUS A: Sponsor HAS already claimed this reward ══
                // "Cairkan Matching Reward 100% Rp5,000,000 ke Sponsor" immediately.
                MatchingRewardLog::create([
                    'sponsor_id'  => $sponsor->id,
                    'downline_id' => $agent->id,
                    'reward_id'   => $reward->id,
                    'claim_id'    => $claim->id,
                    'amount'      => $reward->reward_value, // 100% match
                    'status'      => MatchingRewardStatus::Paid,
                    'paid_at'     => now(),
                ]);

                Log::info("RewardClaim: Kasus A — Immediate matching reward Rp" . number_format((float) $reward->reward_value) . " to Sponsor[{$sponsor->id}] for Agent[{$agent->id}] Claim[{$claim->id}].");

                /*
                 * === DISBURSEMENT HOOK (Kasus A) ===
                 *   $this->payoutGateway->transfer($sponsor->user, $reward->reward_value);
                 */

            } else {

                // ══ KASUS B: Sponsor has NOT yet claimed this reward ══
                // "Simpan ke Tabel matching_reward_logs — Status: PENDING"
                // This record will be triggered/flushed in Step 4 above when
                // the sponsor later gets their own same reward claim approved.
                MatchingRewardLog::create([
                    'sponsor_id'  => $sponsor->id,
                    'downline_id' => $agent->id,
                    'reward_id'   => $reward->id,
                    'claim_id'    => $claim->id,
                    'amount'      => $reward->reward_value,
                    'status'      => MatchingRewardStatus::Pending,
                    'paid_at'     => null,
                ]);

                Log::info("RewardClaim: Kasus B — PENDING matching reward Rp" . number_format((float) $reward->reward_value) . " logged for Sponsor[{$sponsor->id}]. Awaiting sponsor's own claim of Reward[{$reward->id}].");
            }
        });
    }

    /**
     * Admin rejects a reward claim.
     *
     * Points are not deducted and no matching reward logic is triggered.
     *
     * @param RewardClaim $claim
     * @param User        $adminUser
     */
    public function rejectClaim(RewardClaim $claim, User $adminUser): void
    {
        if (! in_array($claim->status, [ClaimStatus::Pending, ClaimStatus::PendingSuperadmin])) {
            throw new \InvalidArgumentException(
                "Claim[{$claim->id}] sudah dalam status '{$claim->status->value}', tidak bisa di-reject."
            );
        }

        DB::transaction(function () use ($claim, $adminUser) {
            $claim->update([
                'status'                    => ClaimStatus::Rejected,
                'approved_by_superadmin_id' => $adminUser->id,
                'approved_at'               => now(), // acts as actioned_at; populated on both approve and reject
            ]);

            Log::warning("RewardClaim: Rejected — Claim[{$claim->id}] by Admin[{$adminUser->id}].");
        });
    }

    /**
     * KASUS B TRIGGER — Flush all PENDING matching reward logs for a given sponsor and reward.
     *
     * Called internally at Step 4 of approveClaim() when the sponsor's OWN claim
     * for this reward is finally approved.
     *
     * FC3 trigger chain:
     *   "Saat Sponsor Klaim Reward-nya Sendiri"
     *   → "Sistem Cek Log PENDING"
     *   → "Cairkan Semua Matching Reward Tertahan"
     *
     * Uses the composite index: (sponsor_id, reward_id, status) = idx_pending_matching_lookup
     *
     * @param Agent  $sponsor The agent who is now receiving their deferred payouts.
     * @param Reward $reward  The reward milestone that triggered the flush.
     */
    private function flushPendingMatchingRewards(Agent $sponsor, Reward $reward): void
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\MatchingRewardLog> $pendingLogs */
        $pendingLogs = MatchingRewardLog::where('sponsor_id', $sponsor->id)
                                        ->where('reward_id', $reward->id)
                                        ->where('status', MatchingRewardStatus::Pending)
                                        ->lockForUpdate()
                                        ->get();

        if ($pendingLogs->isEmpty()) {
            return;
        }

        foreach ($pendingLogs as $log) {
            $log->markAsPaid();

            Log::info("RewardClaim: Kasus B Trigger — Flushed PENDING matching reward Log[{$log->id}] (Rp" . number_format((float) $log->amount) . ") to Sponsor[{$sponsor->id}] from Downline[{$log->downline_id}].");

            /*
             * === DISBURSEMENT HOOK (Kasus B flush) ===
             *   $this->payoutGateway->transfer($sponsor->user, $log->amount);
             */
        }

        Log::info("RewardClaim: Kasus B Trigger — Flushed {$pendingLogs->count()} PENDING matching reward(s) for Sponsor[{$sponsor->id}] Reward[{$reward->id}].");
    }
}
