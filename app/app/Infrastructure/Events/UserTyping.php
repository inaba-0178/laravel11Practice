<?php

namespace App\Infrastructure\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int    $roomId,
        public readonly string $userId,
        public readonly string $userType,
        public readonly string $userName,
        public readonly bool   $isTyping,
    ) {}

    public function broadcastOn(): Channel
    {
        return new PrivateChannel("room.{$this->roomId}");
    }

    public function broadcastAs(): string
    {
        return 'user.typing';
    }
}