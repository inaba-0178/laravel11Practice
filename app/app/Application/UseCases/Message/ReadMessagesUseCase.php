<?php

namespace App\Application\UseCases\Message;

use App\Domain\Message\Repositories\MessageReadRepositoryInterface;

class ReadMessagesUseCase
{
    public function __construct(
        private readonly MessageReadRepositoryInterface $messageReadRepository
    ) {}

    public function execute(array $messageIds, int $userId): void
    {
        foreach ($messageIds as $messageId) {
            $this->messageReadRepository->firstOrCreate($messageId, $userId);
        }
    }
}