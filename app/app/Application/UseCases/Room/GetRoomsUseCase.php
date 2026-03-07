<?php

namespace App\Application\UseCases\Room;

use App\Domain\Room\Repositories\RoomRepositoryInterface;
use Illuminate\Support\Collection;

class GetRoomsUseCase
{
    public function __construct(
        private readonly RoomRepositoryInterface $roomRepository
    ) {}

    public function execute(int $userId): Collection
    {
        return $this->roomRepository->findByUserId($userId);
    }
}