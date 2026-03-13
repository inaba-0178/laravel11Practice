<?php

namespace App\Infrastructure\Repositories\MemberPassword;

use App\Domain\MemberPassword\Repositories\MemberPasswordRepositoryInterface;
use App\Infrastructure\Eloquent\User\Member;

class EloquentMemberPasswordRepository implements MemberPasswordRepositoryInterface
{
    public function findByEmail(string $email): ?object
    {
        return Member::where('email', $email)->first();
    }

    public function updatePassword(string $email, string $hashedPassword): void
    {
        Member::where('email', $email)->update([
            'password' => $hashedPassword,
        ]);
    }
}