<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\RoomInvite;

use App\Domain\RoomInvite\Repositories\RoomInviteRepositoryInterface;
use App\Domain\Shared\Constants\UserType;
use App\Infrastructure\Eloquent\User\RoomUser;
use Illuminate\Support\Collection;

class EloquentRoomInviteRepository implements RoomInviteRepositoryInterface
{
    public function findPendingByUserId(string $userId): Collection
    {
        return RoomUser::where('user_id', $userId)
            ->where('user_type', UserType::MEMBER)
            ->where('status', 'pending')
            ->where(fn ($q) => $q->whereNull('expired_at')->orWhere('expired_at', '>', now()))
            ->with('room')
            ->get();
    }

    public function findByRoomAndUser(int $roomId, string $userId): ?object
    {
        return RoomUser::where('room_id', $roomId)
            ->where('user_id', $userId)
            ->where('user_type', UserType::MEMBER)
            ->where('status', 'pending')
            ->first();
    }

    public function approve(int $roomUserId): void
    {
        RoomUser::where('id', $roomUserId)->update([
            'status'       => 'approved',
            'responded_at' => now(),
        ]);
    }

    public function reject(int $roomUserId): void
    {
        RoomUser::where('id', $roomUserId)->update([
            'status'       => 'rejected',
            'responded_at' => now(),
        ]);
    }
}