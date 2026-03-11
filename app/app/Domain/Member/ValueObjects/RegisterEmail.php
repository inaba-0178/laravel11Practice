<?php

namespace App\Domain\Member\ValueObjects;

final class RegisterEmail
{
    public function __construct(
        public readonly string $email,
    ) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('メールアドレスの形式が正しくありません');
        }
    }
}