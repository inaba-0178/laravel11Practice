<?php

namespace App\Infrastructure\Notifications\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $userName,
        public readonly string $loginAt,
        public readonly string $ipAddress,
        public readonly string $userAgent,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【セキュリティ通知】管理画面へのログインがありました',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.login-notification',
        );
    }
}
