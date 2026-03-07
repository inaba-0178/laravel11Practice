<?php

namespace App\Infrastructure\Repositories\Message;

use App\Domain\Message\Repositories\MessageRepositoryInterface;
use App\Infrastructure\Eloquent\User\Message;
use Illuminate\Support\Collection;

class MessageRepository implements MessageRepositoryInterface
{
    public function findByRoomId(int $roomId): Collection
    {
        return Message::where('room_id', $roomId)
            ->with(['user', 'messageReads'])
            ->orderBy('created_at')
            ->get();
    }

    public function create(array $data): object
    {
        return Message::create($data);
    }
}