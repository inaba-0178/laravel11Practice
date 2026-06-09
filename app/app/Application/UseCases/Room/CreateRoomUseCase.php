<?php

namespace App\Application\UseCases\Room;

use App\Domain\Room\Repositories\RoomRepositoryInterface;

class CreateRoomUseCase
{
    public function __construct(
        private readonly RoomRepositoryInterface $roomRepository
    ) {}

    public function execute(array $data, string $authUserId, string $authUserType): object
    {
        $room = $this->roomRepository->create([
            'name'         => $data['name'] ?? null,
            'type'         => $data['type'],
            'related_type' => $data['related_type'] ?? null,
            'related_id'   => $data['related_id'] ?? null,
            'is_active'    => 1,
        ]);

        // 作成者を参加者に追加
        $users = array_merge(
            [['id' => $authUserId, 'user_type' => $authUserType]],
            $data['users'] ?? []
        );

        // 重複除去
        $uniqueUsers = collect($users)
            ->unique(fn ($u) => $u['id'] . '_' . $u['user_type'])
            ->values()
            ->toArray();

        $this->roomRepository->attachUsers($room->id, $uniqueUsers);

        return $this->roomRepository->findById($room->id);
    }
}