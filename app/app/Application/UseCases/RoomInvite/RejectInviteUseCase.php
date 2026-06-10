<?php

declare(strict_types=1);

namespace App\Application\UseCases\RoomInvite;

use App\Application\Services\MailService;
use App\Domain\RoomInvite\Repositories\RoomInviteRepositoryInterface;
use App\Domain\Shared\Constants\MailTemplateKey;
use App\Domain\Shared\Constants\UserType;
use App\Infrastructure\Eloquent\User\Room;
use App\Models\User;

class RejectInviteUseCase
{
    public function __construct(
        private readonly RoomInviteRepositoryInterface $roomInviteRepository,
        private readonly MailService                   $mailService,
    ) {}

    public function execute(int $roomId, string $userId): void
    {
        $roomUser = $this->roomInviteRepository->findByRoomAndUser($roomId, $userId);

        if (!$roomUser) {
            throw new \RuntimeException('招待が見つかりません');
        }

        $this->roomInviteRepository->reject($roomUser->id);

        $this->notifyDealers($roomId);
    }

    private function notifyDealers(int $roomId): void
    {
        $room = Room::with('roomUsers')->find($roomId);
        if (!$room) return;

        $room->roomUsers
            ->where('user_type', UserType::STAFF)
            ->each(function ($roomUser) use ($room) {
                $staff = User::find($roomUser->user_id);
                if (!$staff) return;

                $this->mailService->send(
                    templateKey:  MailTemplateKey::CHAT_INVITE_REJECTED,
                    toEmail:      $staff->email,
                    placeholders: [
                        'dealer_name' => $staff->name,
                        'room_name'   => $room->name ?? 'ダイレクトメッセージ',
                        'rejected_at' => now()->format('Y/m/d H:i'),
                    ],
                );
            });
    }
}