<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifications\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class CommonMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        private readonly string $mailSubject,
        private readonly string $body,
        private readonly array  $mailAttachments = [],  // ← 名前変更
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.common',
            with: [
                'body' => $this->body,
            ],
        );
    }

    public function attachments(): array
    {
        return $this->mailAttachments;  // ← 名前変更
    }
}