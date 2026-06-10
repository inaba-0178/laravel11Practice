<?php

declare(strict_types=1);

namespace App\Application\UseCases\RoomInvite;

use App\Domain\RoomInvite\Repositories\RoomInviteRepositoryInterface;

class ApproveInviteUseCase
{
    public function __construct(
        private readonly RoomInviteRepositoryInterface $roomInviteRepository,
    ) {}

    public function execute(int $roomId, string $userId): int
    {
        $roomUser = $this->roomInviteRepository->findByRoomAndUser($roomId, $userId);

        if (!$roomUser) {
            throw new \RuntimeException('招待が見つかりません');
        }

        if ($roomUser->expired_at && $roomUser->expired_at < now()) {
            throw new \RuntimeException('招待の有効期限が切れています');
        }

        $this->roomInviteRepository->approve($roomUser->id);

        return $roomId;
    }
}