<?php

namespace App\Domain\Auth\ValueObjects;

final class LoginCredentials
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('メールアドレスの形式が正しくありません');
        }

        if (empty($password)) {
            throw new \InvalidArgumentException('パスワードを入力してください');
        }
    }
}