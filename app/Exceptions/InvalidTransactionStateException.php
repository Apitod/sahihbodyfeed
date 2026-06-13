<?php

namespace App\Exceptions;

use App\Models\Transaction;
use RuntimeException;

/**
 * Thrown when a service method is called on a transaction that is already
 * in a terminal or incompatible state.
 *
 * Examples:
 *   - Attempting to approve an already-verified transaction.
 *   - Attempting to approve a rejected transaction.
 *   - Calling approveRegistration() on a repeat_order transaction.
 */
class InvalidTransactionStateException extends RuntimeException
{
    public function __construct(
        public readonly Transaction $transaction,
        string $message = '',
    ) {
        parent::__construct($message ?: sprintf(
            'Transaksi [#%d] memiliki status "%s" dan tidak dapat diproses untuk operasi ini.',
            $transaction->id,
            $transaction->status?->value ?? 'unknown',
        ));
    }
}
