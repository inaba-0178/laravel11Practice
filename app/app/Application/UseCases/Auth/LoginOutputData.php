<?php
namespace App\Application\UseCases\Auth;

use App\Models\User;

class LoginOutputData
{
    public function __construct(
        private readonly User   $user,    
        private readonly string $token,
    ) {}

    public function getUser(): User
    {
        return $this->user;
    }

    public function toArray(): array
    {
        return [
            'success'   => true,
            'token'     => $this->token,
            'user'      => $this->user,
        ];
    }
}