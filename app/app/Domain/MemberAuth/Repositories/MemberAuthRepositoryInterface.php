<?php

namespace App\Domain\MemberAuth\Repositories;

interface MemberAuthRepositoryInterface
{
    public function findByEmail(string $email): ?object;
}