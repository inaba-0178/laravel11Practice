<?php

namespace App\Domain\MemberPassword\Repositories;

interface MemberPasswordRepositoryInterface
{
    public function findByEmail(string $email): ?object;
    public function updatePassword(string $email, string $hashedPassword): void;
}