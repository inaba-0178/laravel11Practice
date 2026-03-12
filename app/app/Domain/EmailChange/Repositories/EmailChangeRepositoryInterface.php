<?php

declare(strict_types=1);

namespace App\Domain\EmailChange\Repositories;

interface EmailChangeRepositoryInterface
{
    /**
     * メールアドレスを更新する
     */
    public function updateEmail(string $usrUserId, string $newEmail): void;

    /**
     * 指定メールアドレスが他の会員に使用されていないか確認する
     */
    public function isEmailTaken(string $email, string $excludeUsrUserId): bool;
}