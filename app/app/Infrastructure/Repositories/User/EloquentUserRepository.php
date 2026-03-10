<?php

namespace App\Infrastructure\Repositories\User;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Models\User;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function updatePassword(string $email, string $password): void
    {
        User::where('email', $email)->update([
            'password' => $password,
        ]);
    }
}