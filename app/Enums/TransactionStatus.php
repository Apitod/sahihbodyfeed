<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case Pending           = 'pending';            // Baru dari agen — menunggu Admin
    case PendingSuperadmin = 'pending_superadmin'; // Sudah di-review Admin — menunggu Superadmin
    case Approved          = 'approved';           // Final approval oleh Superadmin
    case Rejected          = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::Pending           => 'Menunggu Admin',
            self::PendingSuperadmin => 'Menunggu Superadmin',
            self::Approved          => 'Disetujui',
            self::Rejected          => 'Ditolak',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::Pending           => 'bg-warning-lt',
            self::PendingSuperadmin => 'bg-azure-lt',
            self::Approved          => 'bg-success-lt',
            self::Rejected          => 'bg-danger-lt',
        };
    }
}
