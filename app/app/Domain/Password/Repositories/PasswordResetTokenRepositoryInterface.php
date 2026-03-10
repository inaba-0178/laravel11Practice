<?php

namespace App\Domain\Password\Repositories;

interface PasswordResetTokenRepositoryInterface
{
    public function upsert(string $email, string $token): void;
    public function findByEmail(string $email): ?object;
    public function deleteByEmail(string $email): void;
}