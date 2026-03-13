<?php

namespace App\Domain\Password\Repositories;

use App\Domain\Password\Entities\PasswordResetToken;

interface PasswordResetTokenRepositoryInterface
{
    public function upsert(string $email, string $token): void;
    public function findByEmail(string $email): ?PasswordResetToken;
    public function deleteByEmail(string $email): void;
}