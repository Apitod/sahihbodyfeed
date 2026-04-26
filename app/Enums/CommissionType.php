<?php

namespace App\Enums;

/**
 * Commission types mapped to flowchart triggers.
 *
 * downline_registration : Triggered by a verified new_agent transaction (FC1).
 *   - Gen 1 → Rp450,000
 *   - Gen 2 → Rp100,000
 *   - Gen 3 → Rp100,000
 *
 * repeat_order : Triggered by a verified repeat_order transaction (FC2).
 *   - Gen 1 → Rp250,000 only
 */
enum CommissionType: string
{
    case DownlineRegistration = 'downline_registration';
    case RepeatOrder          = 'repeat_order';

    /**
     * Commission payout amount per generation level (in IDR).
     * Returns 0 if the generation level is not applicable for this type.
     */
    public function amountForGeneration(int $generation): int
    {
        return match($this) {
            self::DownlineRegistration => match($generation) {
                1 => 450_000,
                2 => 100_000,
                3 => 100_000,
                default => 0,
            },
            self::RepeatOrder => match($generation) {
                1 => 250_000,
                default => 0,
            },
        };
    }

    /**
     * Maximum generation depth for this commission type.
     */
    public function maxGenerations(): int
    {
        return match($this) {
            self::DownlineRegistration => 3,
            self::RepeatOrder          => 1,
        };
    }

    /**
     * Derive the CommissionType from a TransactionType.
     */
    public static function fromTransactionType(TransactionType $type): self
    {
        return match($type) {
            TransactionType::NewAgent    => self::DownlineRegistration,
            TransactionType::RepeatOrder => self::RepeatOrder,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::DownlineRegistration => 'Pendaftaran Downline',
            self::RepeatOrder          => 'Repeat Order (RO)',
        };
    }
}
