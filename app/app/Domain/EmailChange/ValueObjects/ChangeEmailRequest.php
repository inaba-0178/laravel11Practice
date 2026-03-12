<?php

declare(strict_types=1);

namespace App\Domain\EmailChange\ValueObjects;

use App\Domain\Shared\ValueObjects\EmailValidator;
use InvalidArgumentException;

final class ChangeEmailRequest
{
    private readonly string $usrUserId;
    private readonly string $newEmail;
    private readonly string $currentEmail;

    public function __construct(string $usrUserId, string $newEmail, string $currentEmail)
    {
        $sanitizedNew     = EmailValidator::validate($newEmail, '新しいメールアドレス');
        $sanitizedCurrent = EmailValidator::validate($currentEmail, '現在のメールアドレス');

        if ($sanitizedNew === $sanitizedCurrent) {
            throw new InvalidArgumentException('新しいメールアドレスは現在と異なるものを入力してください。');
        }

        $this->usrUserId    = $usrUserId;
        $this->newEmail     = $sanitizedNew;
        $this->currentEmail = $sanitizedCurrent;
    }

    public function getUsrUserId(): string
    {
        return $this->usrUserId;
    }

    public function getNewEmail(): string
    {
        return $this->newEmail;
    }

    public function getCurrentEmail(): string
    {
        return $this->currentEmail;
    }
}