<?php
namespace App\Domain\Message\Repositories;

use Illuminate\Support\Collection;

interface MessageReadRepositoryInterface
{
    public function firstOrCreate(int $messageId, int $userId): void;
}