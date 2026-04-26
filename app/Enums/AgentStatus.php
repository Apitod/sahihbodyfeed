<?php

namespace App\Enums;

/**
 * Agent status levels with their point thresholds.
 * Source: Flowchart 3 — "Logika Perubahan Status" box.
 */
enum AgentStatus: string
{
    case Agent       = 'agent';
    case Supervisor  = 'supervisor';
    case AssManager  = 'ass_manager';
    case Manager     = 'manager';

    /**
     * Human-readable label for UI display.
     */
    public function label(): string
    {
        return match($this) {
            self::Agent      => 'Agen',
            self::Supervisor => 'Supervisor',
            self::AssManager => 'Asisten Manager',
            self::Manager    => 'Manager',
        };
    }

    /**
     * Minimum total_points required to achieve this status tier.
     */
    public function requiredPoints(): int
    {
        return match($this) {
            self::Agent      => 0,
            self::Supervisor => 20,
            self::AssManager => 100,
            self::Manager    => 500,
        };
    }

    /**
     * Resolve the highest achievable status for a given point total.
     * Used by RewardClaimService after approving a claim (FC3 — "Update Status Level Agen Otomatis").
     */
    public static function fromPoints(int $points): self
    {
        if ($points >= 500) return self::Manager;
        if ($points >= 100) return self::AssManager;
        if ($points >= 20)  return self::Supervisor;

        return self::Agent;
    }
}
