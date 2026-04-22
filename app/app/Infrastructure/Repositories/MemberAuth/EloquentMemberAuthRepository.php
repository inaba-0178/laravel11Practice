<?php

namespace App\Infrastructure\Repositories\MemberAuth;

use App\Domain\MemberAuth\Repositories\MemberAuthRepositoryInterface;
use App\Infrastructure\Eloquent\User\UsrUser;

class EloquentMemberAuthRepository implements MemberAuthRepositoryInterface
{
    public function findByEmail(string $email): ?UsrUser
    {
        return UsrUser::where('email', $email)->first();
    }
}