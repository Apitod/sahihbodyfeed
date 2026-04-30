<?php

namespace App\Enums;

/**
 * Three-stage commission lifecycle (Feature 3):
 *
 *   menunggu  → Commission generated today; awaiting overnight accumulation.
 *   pending   → Cron (01:00 WITA) has processed it; ready for admin disbursement.
 *   paid      → Admin has confirmed and disbursed the payout.
 */
enum CommissionStatus: string
{
    case Menunggu = 'menunggu';   // Waiting — created today, not yet processed.
    case Pending  = 'pending';    // Processed by cron; awaiting admin payout.
    case Paid     = 'paid';       // Disbursed to the agent.

    /** Human-readable label for UI display. */
    public function label(): string
    {
        return match($this) {
            self::Menunggu => 'Menunggu',
            self::Pending  => 'Diproses',
            self::Paid     => 'Dibayar',
        };
    }

    /** Tailwind/Tabler badge colour class for UI display. */
    public function badgeClass(): string
    {
        return match($this) {
            self::Menunggu => 'bg-yellow-100 text-yellow-800',
            self::Pending  => 'bg-blue-100 text-blue-800',
            self::Paid     => 'bg-green-100 text-green-800',
        };
    }
}
