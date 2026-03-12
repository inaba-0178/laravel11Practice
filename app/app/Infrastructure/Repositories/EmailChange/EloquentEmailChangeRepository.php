<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\EmailChange;

use App\Domain\EmailChange\Repositories\EmailChangeRepositoryInterface;
use App\Infrastructure\Eloquent\User\Member;

final class EloquentEmailChangeRepository implements EmailChangeRepositoryInterface
{
    public function updateEmail(string $usrUserId, string $newEmail): void
    {
        Member::where('id', $usrUserId)->update([
            'email'            => $newEmail,
            'email_changed_at' => now(),
        ]);
    }

    public function isEmailTaken(string $email, string $excludeUsrUserId): bool
    {
        return Member::where('email', $email)
            ->where('id', '!=', $excludeUsrUserId)
            ->exists();
    }
}