<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class CarRegistrationStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string  $carName,
        private readonly string  $status,   // 'approved' or 'rejected'
        private readonly string  $message,  // 管理者コメント or 差し戻し理由
        private readonly int     $carId,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $label = $this->status === 'approved' ? '承認' : '差し戻し';

        return [
            'type'     => "car_registration_{$this->status}",
            'car_id'   => $this->carId,
            'car_name' => $this->carName,
            'status'   => $this->status,
            'message'  => "車両「{$this->carName}」が{$label}されました。",
            'comment'  => $this->message,
        ];
    }
}