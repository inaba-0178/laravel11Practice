<?php

namespace App\Application\UseCases\Auth;

use App\Models\User;

class LogoutUseCase
{
    public function execute(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}