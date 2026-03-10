<?php

namespace App\Infrastructure\Repositories\Password;

use App\Domain\Password\Repositories\PasswordResetTokenRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Domain\Shared\Constants\PasswordPolicy;

class EloquentPasswordResetTokenRepository implements PasswordResetTokenRepositoryInterface
{
    public function upsert(string $email, string $token): void
    {
        DB::connection('user')->table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token'      => hash(PasswordPolicy::HASH_ALGORITHM, $token),
                'created_at' => now(),
            ]
        );
    }

    public function findByEmail(string $email): ?object
    {
        return DB::connection('user')
            ->table('password_reset_tokens')
            ->where('email', $email)
            ->first();
    }

    public function deleteByEmail(string $email): void
    {
        DB::connection('user')
            ->table('password_reset_tokens')
            ->where('email', $email)
            ->delete();
    }
}