<?php

namespace App\Exceptions;

use App\Models\Agent;
use App\Models\Reward;
use RuntimeException;

/**
 * Thrown when an agent attempts to claim a reward but does not have
 * enough total_points to meet the reward's required_points threshold.
 *
 * Corresponds to the "Error: Poin Tidak Cukup" terminal state in Flowchart 3.
 */
class InsufficientPointsException extends RuntimeException
{
    public function __construct(
        public readonly Agent  $agent,
        public readonly Reward $reward,
    ) {
        parent::__construct(sprintf(
            'Agen [%s] hanya memiliki %d poin, butuh %d poin untuk klaim reward [%s].',
            $agent->nama,
            $agent->total_points,
            $reward->required_points,
            $reward->name,
        ));
    }
}
