<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case Pending  = 'pending';
    case Verified = 'verified';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::Pending  => 'Menunggu Verifikasi',
            self::Verified => 'Terverifikasi',
            self::Rejected => 'Ditolak',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::Pending  => 'bg-warning-lt',
            self::Verified => 'bg-success-lt',
            self::Rejected => 'bg-danger-lt',
        };
    }
}
