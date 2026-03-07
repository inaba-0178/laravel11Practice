<?php

namespace App\Application\UseCases\Room;

use App\Domain\Room\Repositories\RoomRepositoryInterface;

class GetRoomUseCase
{
    public function __construct(
        private readonly RoomRepositoryInterface $roomRepository
    ) {}

    public function execute(int $roomId): ?object
    {
        return $this->roomRepository->findById($roomId);
    }
}