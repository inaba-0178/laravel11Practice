<?php

namespace App\Application\UseCases\Message;

use App\Domain\Message\Repositories\MessageRepositoryInterface;

class SendMessageUseCase
{
    public function __construct(
        private readonly MessageRepositoryInterface $messageRepository
    ) {}

    public function execute(int $roomId, string $userId, string $userType, string $message): object
    {
        return $this->messageRepository->create([
            'room_id'   => $roomId,
            'user_id'   => $userId,
            'user_type' => $userType,
            'message'   => $message,
        ]);
    }
}