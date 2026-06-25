<?php

namespace App\Application\UseCases\MemberAuth;

use App\Infrastructure\Eloquent\User\UsrUser;

class MemberLoginOutputData
{
    public function __construct(
        private readonly UsrUser $member,
        private readonly string  $token,
    ) {}

    public function getMember(): UsrUser
    {
        return $this->member;
    }

    public function toArray(): array
    {
        return [
            'success' => true,
            'token'   => $this->token,
            'member'  => [
                'id'       => $this->member->id,
                'nickname' => $this->member->nickname,
                'email'    => $this->member->email,
                'sei'      => $this->member->sei,
                'mei'      => $this->member->mei,
            ],
        ];
    }
}