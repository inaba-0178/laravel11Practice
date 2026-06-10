<?php

declare(strict_types=1);

namespace App\Application\UseCases\Message;

use App\Domain\Message\Repositories\MessageRepositoryInterface;

class SendMessageUseCase
{
    public function __construct(
        private readonly MessageRepositoryInterface $messageRepository,
    ) {}

    public function execute(
        int    $roomId,
        string $userId,
        string $userType,
        string $message,
        array  $attachment = [],
    ): object {
        return $this->messageRepository->create([
            'room_id'          => $roomId,
            'user_id'          => $userId,
            'user_type'        => $userType,
            'message'          => $message,
            'attachment_url'   => $attachment['attachment_url']  ?? null,
            'attachment_type'  => $attachment['attachment_type'] ?? null,
            'attachment_name'  => $attachment['attachment_name'] ?? null,
            'attachment_size'  => $attachment['attachment_size'] ?? null,
        ]);
    }
}