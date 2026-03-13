<?php

namespace App\Infrastructure\Repositories\Password;

use App\Domain\Password\Repositories\PasswordResetTokenRepositoryInterface;
use App\Domain\Shared\Constants\PasswordPolicy;
use App\Domain\Password\Entities\PasswordResetToken;
use App\Infrastructure\Eloquent\User\PasswordResetToken as PasswordResetTokenModel;

class EloquentPasswordResetTokenRepository implements PasswordResetTokenRepositoryInterface
{
    public function upsert(string $email, string $token): void
    {
        PasswordResetTokenModel::updateOrInsert(
            ['email' => $email],
            [
                'token'      => hash(PasswordPolicy::HASH_ALGORITHM, $token),
                'created_at' => now(),
            ]
        );
    }

    public function findByEmail(string $email): ?PasswordResetToken
    {
        $record = PasswordResetTokenModel::where('email', $email)->first();

        if (!$record) {
            return null;
        }

        return new PasswordResetToken(
            email:     $record->email,
            token:     $record->token,
            createdAt: $record->created_at,
        );
    }

    public function deleteByEmail(string $email): void
    {
        PasswordResetTokenModel::where('email', $email)->delete();
    }
}