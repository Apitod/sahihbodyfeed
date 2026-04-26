<?php

namespace App\Enums;

/**
 * Transaction types from Flowchart 1 & 2.
 *
 * new_agent   : Initial registration payment — Rp2,650,000 (FC1)
 * repeat_order: Subsequent product order    — Rp2,350,000 (FC2)
 */
enum TransactionType: string
{
    case NewAgent    = 'new_agent';
    case RepeatOrder = 'repeat_order';

    public function label(): string
    {
        return match($this) {
            self::NewAgent    => 'Registrasi Agen Baru',
            self::RepeatOrder => 'Repeat Order',
        };
    }

    /**
     * The fixed transaction amount for each type (in IDR).
     * FC1 = Rp2,650,000 | FC2 = Rp2,350,000
     */
    public function amount(): int
    {
        return match($this) {
            self::NewAgent    => 2_650_000,
            self::RepeatOrder => 2_350_000,
        };
    }
}
