<?php

namespace App\Models;

use App\Enums\AgentStatus;
use App\Enums\ClaimStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agent extends Model
{
    // NOTE: Status thresholds are now canonical on AgentStatus enum via ->requiredPoints().

    protected $fillable = [
        // ── Core identity ─────────────────────────────────────────────────
        'user_id',
        'nama',
        'no_telp',
        'alamat',

        // ── KTP document ──────────────────────────────────────────────────
        'foto_ktp',

        // ── Bank payout data ──────────────────────────────────────────────
        'bank_name',
        'bank_account',
        'bank_account_name',

        // ── Hierarchy & gamification ──────────────────────────────────────
        'upline_id',
        'total_points',
        'status',
        'joined_at',
    ];



    protected function casts(): array
    {
        return [
            'total_points' => 'integer',
            'joined_at'    => 'datetime',
            'status'       => AgentStatus::class,
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * The user account that owns this agent profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The direct sponsor (upline Gen-1) of this agent.
     * Returns null for top-level agents who have no sponsor.
     */
    public function upline(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'upline_id');
    }

    /**
     * All direct downlines (agents this agent sponsored directly).
     * These are the Gen-1 connections.
     */
    public function downlines(): HasMany
    {
        return $this->hasMany(Agent::class, 'upline_id');
    }

    /**
     * All payment transactions submitted by this agent (registrations + repeat orders).
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * All commissions earned by this agent as an upline recipient.
     * Covers both downline_registration and repeat_order commission types.
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class, 'recipient_id');
    }

    /**
     * All reward milestone claims submitted by this agent.
     */
    public function rewardClaims(): HasMany
    {
        return $this->hasMany(RewardClaim::class);
    }

    /**
     * Matching reward logs where this agent is the SPONSOR (payout recipient).
     * Query `->where('status', 'pending')` to find deferred Kasus-B payouts.
     */
    public function matchingRewardsAsSponsors(): HasMany
    {
        return $this->hasMany(MatchingRewardLog::class, 'sponsor_id');
    }

    /**
     * Matching reward logs this agent TRIGGERED as a downline claimant.
     * Useful for audit history on an agent's profile page.
     */
    public function matchingRewardsAsDownline(): HasMany
    {
        return $this->hasMany(MatchingRewardLog::class, 'downline_id');
    }

    // ─── Helper Methods ───────────────────────────────────────────────────────

    /**
     * Resolve the correct status level for the agent's current total_points.
     * Delegates to AgentStatus::fromPoints() — the single source of truth.
     * Called by RewardClaimService after every approved claim (FC3).
     */
    public function resolveStatus(): AgentStatus
    {
        return AgentStatus::fromPoints($this->total_points);
    }

    /**
     * Check if this agent has already had a reward claim APPROVED for a given reward.
     * Used in FC3 to decide between Kasus A (immediate payout) and Kasus B (PENDING log).
     */
    public function hasApprovedClaimForReward(int $rewardId): bool
    {
        return $this->rewardClaims()
                    ->where('reward_id', $rewardId)
                    ->where('status', ClaimStatus::Approved)
                    ->exists();
    }

    /**
     * Retrieve all PENDING matching reward logs owed to this agent as a sponsor
     * for a specific reward. Called when THIS agent's own reward claim is approved (FC3 Kasus B trigger).
     */
    public function pendingMatchingRewards(int $rewardId)
    {
        return $this->matchingRewardsAsSponsors()
                    ->where('reward_id', $rewardId)
                    ->where('status', 'pending')
                    ->get();
    }
}
