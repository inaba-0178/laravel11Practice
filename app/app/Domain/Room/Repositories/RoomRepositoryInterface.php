<?php

namespace App\Domain\Room\Repositories;

use Illuminate\Support\Collection;

interface RoomRepositoryInterface
{
    public function findByUserId(string $userId, string $userType): Collection;
    public function findById(int $roomId): ?object;
    public function create(array $data): object;
    public function attachUsers(int $roomId, array $users): void;
}