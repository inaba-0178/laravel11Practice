<?php

namespace App\Infrastructure\Repositories\MemberAuth;

use App\Domain\MemberAuth\Repositories\MemberAuthRepositoryInterface;
use App\Infrastructure\Eloquent\User\Member;

class EloquentMemberAuthRepository implements MemberAuthRepositoryInterface
{
    public function findByEmail(string $email): ?Member
    {
        return Member::where('email', $email)->first();
    }
}