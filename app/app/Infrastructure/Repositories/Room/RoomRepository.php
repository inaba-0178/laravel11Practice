<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Room;

use App\Domain\Room\Repositories\RoomRepositoryInterface;
use App\Domain\Shared\Constants\UserType;
use App\Infrastructure\Eloquent\User\Room;
use App\Infrastructure\Eloquent\User\UsrUser;
use App\Models\User;
use Illuminate\Support\Collection;

class RoomRepository implements RoomRepositoryInterface
{
    public function findByUserId(string $userId, string $userType): Collection
    {
        return Room::whereHas('roomUsers', fn ($q) =>
                $q->where('user_id', $userId)->where('user_type', $userType)
            )
            ->where('is_active', 1)
            ->with(['roomUsers', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->get();
    }

    public function findById(int $roomId): ?object
    {
        $room = Room::with(['roomUsers'])->find($roomId);

        if (!$room) return null;

        $mappedUsers = $room->roomUsers->map(fn ($roomUser) => $this->formatRoomUser($roomUser));

        $room->setRelation('roomUsers', $mappedUsers);
        $room->room_users = $mappedUsers;

        return $room;
    }

    public function create(array $data): object
    {
        return Room::create($data);
    }

    public function attachUsers(int $roomId, array $users): void
    {
        $room = Room::find($roomId);
        foreach ($users as $user) {
            $room->roomUsers()->create([
                'user_id'   => $user['id'],
                'user_type' => $user['user_type'],
            ]);
        }
    }

    private function formatRoomUser(object $roomUser): array
    {
        $sender = $roomUser->user_type === UserType::STAFF
            ? User::find($roomUser->user_id)
            : UsrUser::find($roomUser->user_id);

        return [
            'id'        => $roomUser->id,
            'user_id'   => $roomUser->user_id,
            'user_type' => $roomUser->user_type,
            'status'    => $roomUser->status,
            'name'      => UserType::getDisplayName($sender, $roomUser->user_type),
            'email'     => $sender?->email ?? '-',
        ];
    }
}