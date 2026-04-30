<?php

namespace App\Notifications;

use App\Models\Commission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * CommissionReceivedNotification
 *
 * Sent to an Agent's linked User when they receive a commission
 * after a transaction is verified by an admin.
 *
 * Channel: database only.
 * Displayed in the agent notification bell (top-right header).
 */
class CommissionReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Commission $commission,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $commission  = $this->commission;
        $transaction = $commission->transaction;
        $typeLabel   = match($commission->type->value) {
            'downline_registration' => 'Komisi Registrasi Downline',
            'repeat_order'          => 'Komisi Repeat Order',
            default                 => 'Komisi',
        };

        return [
            'commission_id'    => $commission->id,
            'transaction_id'   => $transaction?->id,
            'type'             => $commission->type->value,
            'type_label'       => $typeLabel,
            'amount'           => $commission->amount,
            'generation_level' => $commission->generation_level,
            'message'          => "Anda menerima {$typeLabel} Gen-{$commission->generation_level} sebesar Rp " . number_format($commission->amount),
            'url'              => route('agent.commissions'),
            'icon'             => 'cash',
        ];
    }
}
