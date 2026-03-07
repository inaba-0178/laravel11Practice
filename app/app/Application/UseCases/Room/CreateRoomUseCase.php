<?php

namespace App\Application\UseCases\Room;

use App\Domain\Room\Repositories\RoomRepositoryInterface;

class CreateRoomUseCase
{
    public function __construct(
        private readonly RoomRepositoryInterface $roomRepository
    ) {}

    public function execute(array $data, int $authUserId): object
    {
        $room = $this->roomRepository->create([
            'name' => $data['name'] ?? null,
            'type' => $data['type'],
        ]);

        $userIds = array_unique(array_merge(
            [$authUserId],
            $data['user_ids']
        ));

        $this->roomRepository->attachUsers($room->id, $userIds);

        return $this->roomRepository->findById($room->id);
    }
}