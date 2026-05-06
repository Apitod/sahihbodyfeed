<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'superadmin';
    case Admin      = 'admin';
    case Agent      = 'agent';

    public function label(): string
    {
        return match($this) {
            self::SuperAdmin => 'Super Administrator',
            self::Admin      => 'Administrator',
            self::Agent      => 'Agen',
        };
    }

    /**
     * Whether this role belongs to the "staff" tier (non-agent).
     */
    public function isStaff(): bool
    {
        return $this !== self::Agent;
    }
}
