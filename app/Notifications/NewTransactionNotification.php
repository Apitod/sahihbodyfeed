<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * NewTransactionNotification
 *
 * Sent to all admin users when an agent submits any new transaction
 * (new_agent registration OR repeat order).
 *
 * Channel: database only.
 * Displayed in the admin notification bell (top-right header).
 */
class NewTransactionNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Transaction $transaction,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $agent   = $this->transaction->agent;
        $typeMap = [
            'new_agent'    => 'Registrasi Agen Baru',
            'repeat_order' => 'Repeat Order',
        ];
        $typeLabel = $typeMap[$this->transaction->type->value] ?? $this->transaction->type->value;

        return [
            'transaction_id' => $this->transaction->id,
            'agent_id'       => $agent?->id,
            'agent_name'     => $agent?->nama ?? '—',
            'type'           => $this->transaction->type->value,
            'type_label'     => $typeLabel,
            'amount'         => $this->transaction->amount,
            'message'        => "{$typeLabel} dari {$agent?->nama} memerlukan verifikasi.",
            'url'            => route('admin.verifications.transactions'),
            'icon'           => 'receipt',
        ];
    }
}
