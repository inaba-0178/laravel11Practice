<?php

namespace App\Infrastructure\Repositories\MessageRead;

use App\Domain\Message\Repositories\MessageReadRepositoryInterface;
use App\Infrastructure\Eloquent\User\MessageRead;

class MessageReadRepository implements MessageReadRepositoryInterface
{
    public function firstOrCreate(int $messageId, string $userId, string $userType): void
    {
        MessageRead::firstOrCreate(
            [
                'message_id' => $messageId,
                'user_id'    => $userId,
                'user_type'  => $userType,
            ],
            ['read_at' => now()]
        );
    }
}