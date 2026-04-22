<?php

namespace App\Infrastructure\Repositories\MemberPassword;

use App\Domain\MemberPassword\Repositories\MemberPasswordRepositoryInterface;
use App\Infrastructure\Eloquent\User\UsrUser;

class EloquentMemberPasswordRepository implements MemberPasswordRepositoryInterface
{
    public function findByEmail(string $email): ?object
    {
        return UsrUser::where('email', $email)->first();
    }

    public function updatePassword(string $email, string $hashedPassword): void
    {
        UsrUser::where('email', $email)->update([
            'password' => $hashedPassword,
        ]);
    }
}