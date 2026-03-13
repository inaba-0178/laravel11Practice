<?php

namespace App\Domain\Password\Entities;

final class PasswordResetToken
{
    public function __construct(
        public readonly string $email,
        public readonly string $token,
        public readonly string $createdAt,
    ) {}
}