<?php

namespace App\Domain\EditMember\Repositories;

use App\Domain\Member\Entities\Member;

interface EditMemberRepositoryInterface
{
    public function findById(string $id): ?Member;
    public function update(string $id, array $data): Member;
}