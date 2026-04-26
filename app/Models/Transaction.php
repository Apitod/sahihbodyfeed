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
        'verified_by',
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
     * The admin user who verified this transaction.
     * Returns null if the transaction is still pending verification.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
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
