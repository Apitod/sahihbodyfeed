<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reward extends Model
{
    protected $fillable = [
        'name',
        'required_points',
        'reward_value',
    ];

    protected function casts(): array
    {
        return [
            'required_points' => 'integer',
            'reward_value'    => 'decimal:2',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * All agent claims made against this reward milestone.
     */
    public function rewardClaims(): HasMany
    {
        return $this->hasMany(RewardClaim::class);
    }

    /**
     * All matching reward log entries associated with this reward milestone.
     * Used to audit total matching payouts triggered per reward tier.
     */
    public function matchingRewardLogs(): HasMany
    {
        return $this->hasMany(MatchingRewardLog::class);
    }
}
