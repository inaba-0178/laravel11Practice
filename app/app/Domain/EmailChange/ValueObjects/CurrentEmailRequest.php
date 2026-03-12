<?php

declare(strict_types=1);

namespace App\Domain\EmailChange\ValueObjects;

use App\Domain\Shared\ValueObjects\EmailValidator;

final class CurrentEmailRequest
{
    private readonly string $currentEmail;

    public function __construct(string $currentEmail)
    {
        $this->currentEmail = EmailValidator::validate($currentEmail, '現在のメールアドレス');
    }

    public function getCurrentEmail(): string
    {
        return $this->currentEmail;
    }
}