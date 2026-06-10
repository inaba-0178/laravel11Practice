<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Message;

use App\Domain\Message\Repositories\MessageRepositoryInterface;
use App\Domain\Shared\Constants\UserType;
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
            ->map(fn ($message) => $this->formatMessage($message));
    }

    public function create(array $data): object
    {
        return Message::create($data);
    }

    private function formatMessage(Message $message): Message
    {
        $sender = $message->sender;

        $message->user = $sender ? [
            'id'        => (string) $sender->id,
            'name'      => UserType::getDisplayName($sender, $message->user_type),
            'user_type' => $message->user_type,
        ] : null;

        return $message;
    }
}