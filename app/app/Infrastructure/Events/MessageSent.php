<?php

declare(strict_types=1);

namespace App\Infrastructure\Events;

use App\Domain\Shared\Constants\UserType;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
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
    public readonly ?array $attachment;

    public function __construct(
        int    $roomId,
        string $userId,
        string $userType,
        int    $id,
        string $message,
        string $createdAt,
        object $userModel,
        ?array $attachment = null,
    ) {
        $this->roomId     = $roomId;
        $this->userId     = $userId;
        $this->userType   = $userType;
        $this->id         = $id;
        $this->message    = $message;
        $this->createdAt  = $createdAt;
        $this->attachment = $attachment;
        $this->user       = [
            'id'        => (string) $userModel->id,
            'name'      => UserType::getDisplayName($userModel, $userType),
            'user_type' => $userType,
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new Channel("room.{$this->roomId}"),
            new PrivateChannel("room.{$this->roomId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}