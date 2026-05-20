<?php

declare(strict_types=1);

namespace App\Domain\CarUpload\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class BulkCarRegistrationPendingNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $dealerName,
        private readonly int    $carCount,
        private readonly array  $carIds,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'        => 'bulk_car_registration_pending',
            'car_ids'     => $this->carIds,
            'car_count'   => $this->carCount,
            'dealer_name' => $this->dealerName,
            'message'     => "{$this->dealerName} が車両を {$this->carCount}台 一括登録しました。確認してください。",
        ];
    }
}