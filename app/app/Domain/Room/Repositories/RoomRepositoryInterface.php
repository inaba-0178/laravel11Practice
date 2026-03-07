<?php

namespace App\Domain\Room\Repositories;

use Illuminate\Support\Collection;

interface RoomRepositoryInterface
{
    public function findByUserId(int $userId): Collection;
    public function findById(int $roomId): ?object;
    public function create(array $data): object;
    public function attachUsers(int $roomId, array $userIds): void;
}