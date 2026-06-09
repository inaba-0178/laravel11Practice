<?php

namespace App\Domain\Message\Repositories;

use Illuminate\Support\Collection;

interface MessageRepositoryInterface
{
    public function findByRoomId(int $roomId): Collection;
    public function create(array $data): object;
}