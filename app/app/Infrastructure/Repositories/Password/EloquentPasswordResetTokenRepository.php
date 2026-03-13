<?php

namespace App\Infrastructure\Repositories\Password;

use App\Domain\Password\Repositories\PasswordResetTokenRepositoryInterface;
use App\Domain\Shared\Constants\PasswordPolicy;
use App\Infrastructure\Eloquent\User\PasswordResetToken;

class EloquentPasswordResetTokenRepository implements PasswordResetTokenRepositoryInterface
{
    public function upsert(string $email, string $token): void
    {
        PasswordResetToken::updateOrInsert(
            ['email' => $email],
            [
                'token'      => hash(PasswordPolicy::HASH_ALGORITHM, $token),
                'created_at' => now(),
            ]
        );
    }

    public function findByEmail(string $email): ?object
    {
        return PasswordResetToken::where('email', $email)->first();
    }

    public function deleteByEmail(string $email): void
    {
        PasswordResetToken::where('email', $email)->delete();
    }
}