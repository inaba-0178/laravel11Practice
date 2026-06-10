<?php

declare(strict_types=1);

namespace App\Domain\RoomInvite\Repositories;

use Illuminate\Support\Collection;

interface RoomInviteRepositoryInterface
{
    public function findPendingByUserId(string $userId): Collection;
    public function findByRoomAndUser(int $roomId, string $userId): ?object;
    public function approve(int $roomUserId): void;
    public function reject(int $roomUserId): void;
}