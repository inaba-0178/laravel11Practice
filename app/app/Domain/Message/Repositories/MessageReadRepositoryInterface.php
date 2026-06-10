<?php

namespace App\Domain\Message\Repositories;

interface MessageReadRepositoryInterface
{
    public function firstOrCreate(int $messageId, string $userId, string $userType): void;
}