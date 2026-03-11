<?php

namespace App\Domain\Member\Repositories;

use App\Domain\Member\Entities\Member;

interface MemberRepositoryInterface
{
    public function create(array $data): Member;
    public function findByEmail(string $email): ?Member;
}