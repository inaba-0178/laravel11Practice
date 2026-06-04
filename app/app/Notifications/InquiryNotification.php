<?php

namespace App\Notifications;

use App\Infrastructure\Eloquent\User\StkInquiry;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class InquiryNotification extends Notification
{
    public function __construct(
        private readonly StkInquiry $inquiry
    ) {}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        $typeLabels = [
            'stock_check'     => '在庫確認',
            'estimate'        => '見積依頼',
            'condition_check' => '車両状態確認',
            'other'           => 'その他',
        ];

        return (new MailMessage)
            ->subject('【新着問い合わせ】' . ($typeLabels[$this->inquiry->inquiry_type] ?? '問い合わせ'))
            ->line('新しい問い合わせが届きました。')
            ->line('種別：' . ($typeLabels[$this->inquiry->inquiry_type] ?? '-'))
            ->line('名前：' . ($this->inquiry->name ?? '会員'))
            ->line('メール：' . ($this->inquiry->email ?? '-'))
            ->line('電話：' . ($this->inquiry->phone ?? '-'))
            ->line('内容：' . ($this->inquiry->message ?? '-'));
    }
}