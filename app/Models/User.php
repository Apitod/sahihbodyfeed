<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    protected $fillable = [
        'username',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password'  => 'hashed',
            'is_active' => 'boolean',
            'role'      => UserRole::class,
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * The agent profile linked to this user account (one-to-one).
     */
    public function agent(): HasOne
    {
        return $this->hasOne(Agent::class);
    }

    /**
     * Transactions this admin user has verified.
     * (verified_by FK on transactions table)
     */
    public function verifiedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'verified_by');
    }

    /**
     * Reward claims this admin user has approved or rejected.
     * (approved_by FK on reward_claims table)
     */
    public function approvedRewardClaims(): HasMany
    {
        return $this->hasMany(RewardClaim::class, 'approved_by');
    }

    // ─── Helper Methods ───────────────────────────────────────────────────────

    /**
     * Convenience check using the cached role column — avoids a pivot-table join.
     * Always keep in sync with Spatie role assignment.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isAgent(): bool
    {
        return $this->role === UserRole::Agent;
    }
}
