<?php

namespace App\Application\UseCases\MemberAuth;

use App\Infrastructure\Eloquent\User\Member;

class MemberLoginOutputData
{
    public function __construct(
        private readonly Member $member,
        private readonly string $token,
    ) {}

    public function toArray(): array
    {
        return [
            'success' => true,
            'token'   => $this->token,
            'member'  => $this->member,
        ];
    }
}