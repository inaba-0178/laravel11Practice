<?php

declare(strict_types=1);

namespace App\Application\UseCases\RoomInvite;

use App\Domain\RoomInvite\Repositories\RoomInviteRepositoryInterface;
use Illuminate\Support\Collection;

class GetPendingInvitesUseCase
{
    public function __construct(
        private readonly RoomInviteRepositoryInterface $roomInviteRepository,
    ) {}

    public function execute(string $userId): Collection
    {
        return $this->roomInviteRepository->findPendingByUserId($userId)
            ->map(fn ($roomUser) => [
                'room_id'    => $roomUser->room_id,
                'room_name'  => $roomUser->room?->name ?? 'ダイレクトメッセージ',
                'invited_at' => $roomUser->invited_at?->format('Y/m/d H:i'),
                'expired_at' => $roomUser->expired_at?->format('Y/m/d H:i'),
            ]);
    }
}