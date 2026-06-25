<?php

namespace App\Infrastructure\Notifications\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoFactorReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $userName,
        public readonly string $adminUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【重要】2段階認証の設定をお願いします',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.two-factor-reminder',
        );
    }
}
