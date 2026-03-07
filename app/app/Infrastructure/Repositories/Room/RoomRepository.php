<?php

namespace App\Infrastructure\Repositories\Room;

use App\Domain\Room\Repositories\RoomRepositoryInterface;
use App\Infrastructure\Eloquent\User\Room;
use Illuminate\Support\Collection;

class RoomRepository implements RoomRepositoryInterface
{
    public function findByUserId(int $userId): Collection
    {
        return Room::whereHas('users', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with(['users', 'messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->get();
    }

    public function findById(int $roomId): ?object
    {
        return Room::with('users')->find($roomId);
    }

    public function create(array $data): object
    {
        return Room::create($data);
    }

    public function attachUsers(int $roomId, array $userIds): void
    {
        Room::find($roomId)->users()->attach($userIds);
    }
}