<?php

namespace App\Domain\Shared\Constants;

final class PasswordPolicy
{
    public const MIN_LENGTH                 = 12;
    public const RESET_TOKEN_EXPIRE_MINUTES = 30;
    public const TOKEN_LENGTH               = 64;
    public const HASH_ALGORITHM             = 'sha256';

    public static function validate(string $password): void
    {
        if (strlen($password) < self::MIN_LENGTH) {
            throw new \InvalidArgumentException(
                'パスワードは' . self::MIN_LENGTH . '文字以上で入力してください'
            );
        }
    }
}