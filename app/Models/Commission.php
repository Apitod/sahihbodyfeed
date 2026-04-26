<?php

namespace App\Models;

use App\Enums\CommissionStatus;
use App\Enums\CommissionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    // NOTE: Commission amounts are now canonical on CommissionType enum via ->amountForGeneration().

    protected $fillable = [
        'transaction_id',
        'recipient_id',
        'amount',
        'generation_level',
        'type',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'           => 'decimal:2',
            'generation_level' => 'integer',
            'type'             => CommissionType::class,
            'status'           => CommissionStatus::class,
            'paid_at'          => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * The transaction that triggered this commission.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * The upline agent receiving this commission payout.
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'recipient_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', CommissionStatus::Pending);
    }

    public function scopePaid($query)
    {
        return $query->where('status', CommissionStatus::Paid);
    }

    /**
     * Resolve the correct commission amount. Delegates to CommissionType enum.
     * Used by CommissionDistributionService.
     */
    public static function resolveAmount(CommissionType $type, int $generationLevel): int
    {
        return $type->amountForGeneration($generationLevel);
    }
}
