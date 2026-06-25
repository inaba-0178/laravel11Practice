<?php

namespace App\Infrastructure\Notifications\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoFactorSetupMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $userName,
        public readonly string $confirmedAt,
        public readonly string $ipAddress,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【セキュリティ通知】2段階認証が設定されました',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.two-factor-setup',
        );
    }
}
