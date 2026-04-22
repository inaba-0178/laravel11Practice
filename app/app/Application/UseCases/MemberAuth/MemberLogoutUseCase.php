<?php

namespace App\Application\UseCases\MemberAuth;

use App\Infrastructure\Eloquent\User\UsrUser;

class MemberLogoutUseCase
{
    public function execute(Member $member): void
    {
        $member->currentAccessToken()->delete();
    }
}