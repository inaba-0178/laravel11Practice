<?php

namespace App\Domain\ChangePassword\Repositories;

interface ChangePasswordRepositoryInterface
{
    public function findById(string $id): ?object;
    public function changePassword(string $id, string $hashedPassword): void;
}