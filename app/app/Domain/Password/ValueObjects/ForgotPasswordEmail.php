<?php

namespace App\Domain\Password\ValueObjects;

final class ForgotPasswordEmail
{
    public function __construct(
        public readonly string $email,
    ) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('メールアドレスの形式が正しくありません');
        }
    }
}