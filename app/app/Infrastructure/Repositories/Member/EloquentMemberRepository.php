<?php

namespace App\Infrastructure\Repositories\Member;

use App\Domain\Member\Repositories\MemberRepositoryInterface;
use App\Infrastructure\Eloquent\User\Member;

class EloquentMemberRepository implements MemberRepositoryInterface
{
    public function create(array $data): Member
    {
        return Member::create($data);
    }

    public function findByEmail(string $email): ?Member
    {
        return Member::where('email', $email)->first();
    }
}