<?php

namespace App\Infrastructure\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int    $roomId,
        public readonly int    $userId,
        public readonly string $userName,
        public readonly bool   $isTyping,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel("room.{$this->roomId}");
    }

    public function broadcastAs(): string
    {
        return 'user.typing';
    }
}