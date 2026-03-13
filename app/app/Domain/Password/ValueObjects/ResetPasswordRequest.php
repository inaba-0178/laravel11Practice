<?php

namespace App\Domain\Password\ValueObjects;

use App\Domain\Shared\Constants\PasswordPolicy;
use App\Domain\Shared\ValueObjects\XssValidator;

final class ResetPasswordRequest
{
    public function __construct(
        public readonly string $token,
        public readonly string $email,
        public readonly string $password,
    ) {
        if (empty($token)) {
            throw new \InvalidArgumentException('トークンが無効です');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('メールアドレスの形式が正しくありません');
        }

        PasswordPolicy::validate($this->password);

        if (XssValidator::check($this->password)) {
            throw new \InvalidArgumentException('使用できない文字が含まれています');
        }
    }
}