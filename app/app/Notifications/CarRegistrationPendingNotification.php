<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

final class CarRegistrationPendingNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $carName,
        private readonly string $dealerName,
        private readonly int    $carId,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'        => 'car_registration_pending',
            'car_id'      => $this->carId,
            'car_name'    => $this->carName,
            'dealer_name' => $this->dealerName,
            'message'     => "{$this->dealerName} が車両「{$this->carName}」を仮登録しました。確認してください。",
        ];
    }
}