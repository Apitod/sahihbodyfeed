<?php

namespace App\Models;

use App\Enums\ClaimStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RewardClaim extends Model
{
    protected $fillable = [
        'agent_id',
        'reward_id',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'status'      => ClaimStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * The agent who submitted this reward claim.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * The reward milestone being claimed.
     * Use this to access reward.required_points and reward.reward_value.
     */
    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    /**
     * The admin user who approved or rejected this claim.
     * Null while the claim is still pending.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Matching reward log entries that were created as a result of THIS claim.
     * (The downline's approved claim → creates matching log for the sponsor.)
     *
     * One RewardClaim can produce exactly ONE MatchingRewardLog entry
     * (for the direct sponsor, either Kasus A or Kasus B).
     */
    public function matchingRewardLogs(): HasMany
    {
        return $this->hasMany(MatchingRewardLog::class, 'claim_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', ClaimStatus::Pending);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', ClaimStatus::Approved);
    }
}
