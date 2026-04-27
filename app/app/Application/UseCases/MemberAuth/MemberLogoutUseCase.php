<?php

namespace App\Application\UseCases\MemberAuth;

use App\Infrastructure\Eloquent\User\UsrUser;

class MemberLogoutUseCase
{
    public function execute(UsrUser $member): void
    {
        $member->currentAccessToken()->delete();
    }
}