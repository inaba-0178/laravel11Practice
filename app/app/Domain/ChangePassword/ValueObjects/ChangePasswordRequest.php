<?php

namespace App\Domain\ChangePassword\ValueObjects;

use App\Domain\Shared\Constants\PasswordPolicy;

final class ChangePasswordRequest
{
    public function __construct(
        public readonly string $currentPassword,
        public readonly string $newPassword,
        public readonly string $newPasswordConfirmation,
    ) {
        if (empty($this->currentPassword)) {
            throw new \InvalidArgumentException('現在のパスワードを入力してください');
        }

        PasswordPolicy::validate($this->newPassword);

        if ($this->newPassword !== $this->newPasswordConfirmation) {
            throw new \InvalidArgumentException('新しいパスワードと確認用パスワードが一致しません');
        }
    }
}