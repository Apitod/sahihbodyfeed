<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    // NOTE: Transaction amounts are now canonical on TransactionType enum via ->amount().

    protected $fillable = [
        'agent_id',
        'type',
        'amount',
        'status',
        'proof_of_payment',
        'verified_by_admin_id',
        'verified_by_superadmin_id',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'      => 'decimal:2',
            'type'        => TransactionType::class,
            'status'      => TransactionStatus::class,
            'verified_at' => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * The agent who submitted this transaction.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * The admin (Tier 2) who did the initial review of this transaction.
     */
    public function adminVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_admin_id');
    }

    /**
     * The superadmin (Tier 1) who gave final approval for this transaction.
     * Returns null if the transaction is still pending final verification.
     */
    public function superadminVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_superadmin_id');
    }

    /**
     * @deprecated Use superadminVerifier() for clarity with the new 3-tier system.
     */
    public function verifier(): BelongsTo
    {
        return $this->superadminVerifier();
    }

    /**
     * Commission rows generated from this transaction after verification.
     * A new_agent transaction produces up to 3 commission rows (Gen 1, 2, 3).
     * A repeat_order transaction produces exactly 1 commission row (Gen 1 only).
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', TransactionStatus::Pending);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', TransactionStatus::Verified);
    }

    public function scopeNewAgent($query)
    {
        return $query->where('type', TransactionType::NewAgent);
    }

    public function scopeRepeatOrder($query)
    {
        return $query->where('type', TransactionType::RepeatOrder);
    }
}
