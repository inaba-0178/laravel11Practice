<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\EmailChange;

use App\Domain\EmailChange\Repositories\EmailChangeRepositoryInterface;
use App\Infrastructure\Eloquent\User\UsrUser;

final class EloquentEmailChangeRepository implements EmailChangeRepositoryInterface
{
    public function updateEmail(string $usrUserId, string $newEmail): void
    {
        UsrUser::where('id', $usrUserId)->update([
            'email'            => $newEmail,
            'email_changed_at' => now(),
        ]);
    }

    public function isEmailTaken(string $email, string $excludeUsrUserId): bool
    {
        return UsrUser::where('email', $email)
            ->where('id', '!=', $excludeUsrUserId)
            ->exists();
    }
}