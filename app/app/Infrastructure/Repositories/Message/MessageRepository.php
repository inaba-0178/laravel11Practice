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
            ->with(['messageReads'])
            ->orderBy('created_at')
            ->get()
            ->map(function ($message) {
                $sender = $message->sender;
                $message->user = $sender ? [
                    'id'        => (string) $sender->id,
                    'name'      => $message->user_type === 'staff'
                        ? $sender->name
                        : $sender->sei . $sender->mei,
                    'user_type' => $message->user_type,
                ] : null;
                return $message;
            });
    }

    public function create(array $data): object
    {
        return Message::create($data);
    }
}