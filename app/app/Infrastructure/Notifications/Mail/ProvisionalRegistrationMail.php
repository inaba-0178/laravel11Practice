<?php

namespace App\Infrastructure\Notifications\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProvisionalRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $token,
        public readonly string $email,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '会員登録のご案内',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.provisional-registration',
        );
    }
}