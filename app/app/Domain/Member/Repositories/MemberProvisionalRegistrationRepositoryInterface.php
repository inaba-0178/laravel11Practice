<?php

namespace App\Domain\Member\Repositories;

interface MemberProvisionalRegistrationRepositoryInterface
{
    public function upsert(string $email, string $token): void;
    public function findByEmail(string $email): ?object;
    public function deleteByEmail(string $email): void;
}