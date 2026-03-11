<?php

namespace App\Domain\Member\Repositories;

use App\Infrastructure\Eloquent\User\Member;

interface MemberRepositoryInterface
{
    public function create(array $data): Member;
    public function findByEmail(string $email): ?Member;
}