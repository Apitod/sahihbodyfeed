<?php

namespace App\Enums;

/**
 * Status for matching_reward_logs rows.
 * Directly models the two cases from Flowchart 3:
 *
 * Pending : Kasus B — sponsor has NOT yet claimed the same reward.
 *           Row waits until the sponsor's own claim is approved.
 * Paid    : Kasus A immediate payout, OR a Kasus B row that has been triggered.
 */
enum MatchingRewardStatus: string
{
    case Pending = 'pending';
    case Paid    = 'paid';
}
