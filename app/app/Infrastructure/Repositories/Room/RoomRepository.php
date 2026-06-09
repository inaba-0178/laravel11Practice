<?php

namespace App\Infrastructure\Repositories\Room;

use App\Domain\Room\Repositories\RoomRepositoryInterface;
use App\Infrastructure\Eloquent\User\Room;
use Illuminate\Support\Collection;

class RoomRepository implements RoomRepositoryInterface
{
    public function findByUserId(string $userId, string $userType): Collection
    {
        return Room::whereHas('roomUsers', function ($query) use ($userId, $userType) {
                $query->where('user_id', $userId)
                      ->where('user_type', $userType);
            })
            ->where('is_active', 1)
            ->with(['roomUsers', 'messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->get();
    }

    public function findById(int $roomId): ?object
    {
        return Room::with('roomUsers')->find($roomId);
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
}