<?php

namespace App\Models;

use App\Enums\MatchingRewardStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchingRewardLog extends Model
{
    protected $fillable = [
        'sponsor_id',
        'downline_id',
        'reward_id',
        'claim_id',
        'amount',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'  => 'decimal:2',
            'status'  => MatchingRewardStatus::class,
            'paid_at' => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * The sponsor (upline) agent who receives or will receive the matching payout.
     * When status = 'pending', this agent has not yet claimed the same reward.
     * When status = 'paid',    the payout has been disbursed to this agent.
     */
    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'sponsor_id');
    }

    /**
     * The downline agent whose approved reward claim CREATED this log entry.
     * This is the "trigger" downline from Flowchart 3.
     */
    public function downline(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'downline_id');
    }

    /**
     * The reward milestone associated with this matching reward entry.
     */
    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    /**
     * The specific reward_claims row that triggered the creation of this log entry.
     * This links back to the downline's approved claim for full audit traceability.
     */
    public function claim(): BelongsTo
    {
        return $this->belongsTo(RewardClaim::class, 'claim_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Kasus B: deferred matching rewards awaiting sponsor's own claim.
     * Critical scope — used by the FC3 trigger query when a sponsor's claim is approved.
     *
     * Usage: MatchingRewardLog::pending()->where('sponsor_id', $id)->where('reward_id', $id)->get()
     */
    public function scopePending($query)
    {
        return $query->where('status', MatchingRewardStatus::Pending);
    }

    public function scopePaid($query)
    {
        return $query->where('status', MatchingRewardStatus::Paid);
    }

    // ─── Helper Methods ───────────────────────────────────────────────────────

    /**
     * Mark this matching reward as paid and record the disbursement timestamp.
     * Call this inside a DB transaction when disbursing the Kasus-B deferred payout.
     */
    public function markAsPaid(): bool
    {
        return $this->update([
            'status'  => MatchingRewardStatus::Paid,
            'paid_at' => now(),
        ]);
    }
}
