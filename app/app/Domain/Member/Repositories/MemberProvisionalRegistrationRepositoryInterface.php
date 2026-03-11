<?php

namespace App\Domain\Member\Repositories;

use App\Domain\Member\Entities\ProvisionalRegistration;

interface MemberProvisionalRegistrationRepositoryInterface
{
    public function upsert(string $email, string $token): void;
    public function findByEmail(string $email): ?ProvisionalRegistration;
    public function deleteByEmail(string $email): void;
}