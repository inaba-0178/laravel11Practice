<?php

namespace App\Application\UseCases\MemberAuth;

use App\Infrastructure\Eloquent\User\Member;

class MemberLogoutUseCase
{
    public function execute(Member $member): void
    {
        $member->currentAccessToken()->delete();
    }
}