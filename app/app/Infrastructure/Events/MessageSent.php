<?php

declare(strict_types=1);

namespace App\Infrastructure\Events;

use App\Domain\Shared\Constants\UserType;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public readonly int    $roomId;
    public readonly string $userId;
    public readonly string $userType;
    public readonly int    $id;
    public readonly string $message;
    public readonly string $createdAt;
    public readonly array  $user;

    public function __construct(
        int    $roomId,
        string $userId,
        string $userType,
        int    $id,
        string $message,
        string $createdAt,
        object $userModel,
    ) {
        $this->roomId    = $roomId;
        $this->userId    = $userId;
        $this->userType  = $userType;
        $this->id        = $id;
        $this->message   = $message;
        $this->createdAt = $createdAt;
        $this->user      = [
            'id'        => (string) $userModel->id,
            'name'      => UserType::getDisplayName($userModel, $userType),
            'user_type' => $userType,
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