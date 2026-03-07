<?php

namespace App\Application\UseCases\Message;

use App\Domain\Message\Repositories\MessageRepositoryInterface;
use Illuminate\Support\Collection;

class GetMessagesUseCase
{
    public function __construct(
        private readonly MessageRepositoryInterface $messageRepository
    ) {}

    public function execute(int $roomId): Collection
    {
        return $this->messageRepository->findByRoomId($roomId);
    }
}