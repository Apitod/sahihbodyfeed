<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, Notifiable;

    protected $fillable = [
        'username',
        'nama',
        'email',
        'no_telp',
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
     * Transactions this user has verified as Admin (Tier 2).
     */
    public function verifiedAsAdminTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'verified_by_admin_id');
    }

    /**
     * Transactions this user has verified as Superadmin (Tier 1).
     */
    public function verifiedAsSuperadminTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'verified_by_superadmin_id');
    }

    /**
     * Reward claims this user has reviewed as Admin (Tier 2).
     */
    public function reviewedRewardClaims(): HasMany
    {
        return $this->hasMany(RewardClaim::class, 'verified_by_admin_id');
    }

    /**
     * Reward claims this user has approved/rejected as Superadmin (Tier 1).
     */
    public function approvedRewardClaims(): HasMany
    {
        return $this->hasMany(RewardClaim::class, 'approved_by_superadmin_id');
    }

    // ─── Helper Methods ───────────────────────────────────────────────────────

    /**
     * Convenience check using the cached role column — avoids a pivot-table join.
     * Always keep in sync with Spatie role assignment.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isAgent(): bool
    {
        return $this->role === UserRole::Agent;
    }

    /**
     * Returns true for both superadmin and admin (non-agent staff).
     */
    public function isStaff(): bool
    {
        return in_array($this->role, [UserRole::SuperAdmin, UserRole::Admin]);
    }
}
