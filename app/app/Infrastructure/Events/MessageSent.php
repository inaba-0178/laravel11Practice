<?php

namespace App\Infrastructure\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class MessageSent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public readonly int    $roomId;
    public readonly int    $userId;
    public readonly int    $id;
    public readonly string $message;
    public readonly string $createdAt;
    public readonly array  $user;

    public function __construct(
        int    $roomId,
        int    $userId,
        int    $id,
        string $message,
        string $createdAt,
        User $userModel,
    ) {
        $this->roomId    = $roomId;
        $this->userId    = $userId;
        $this->id        = $id;
        $this->message   = $message;
        $this->createdAt = $createdAt;
        $this->user = [
            'id'   => $userModel->id,
            'name' => $userModel->name,
        ];
    }

    public function broadcastOn(): Channel
    {
        return new Channel("room.{$this->roomId}");
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}