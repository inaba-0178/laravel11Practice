<?php

namespace App\Domain\MemberAuth\Repositories;

use App\Infrastructure\Eloquent\User\Member;

interface MemberAuthRepositoryInterface
{
    public function findByEmail(string $email): ?Member;
}