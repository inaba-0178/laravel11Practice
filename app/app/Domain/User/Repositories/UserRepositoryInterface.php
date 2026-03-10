<?php

namespace App\Domain\User\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function updatePassword(string $email, string $password): void;
}