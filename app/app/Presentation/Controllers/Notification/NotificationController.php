<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Notification;

use App\Domain\Shared\Constants\UserType;
use App\Http\Controllers\Controller;
use App\Infrastructure\Eloquent\User\Message;
use App\Infrastructure\Eloquent\User\MessageRead;
use App\Infrastructure\Eloquent\User\RoomUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        $userId = (string) $request->user()->id;

        // 未承認招待数
        $chatInvites = RoomUser::where('user_id', $userId)
            ->where('user_type', UserType::MEMBER)
            ->where('status', 'pending')
            ->where(fn ($q) => $q->whereNull('expired_at')->orWhere('expired_at', '>', now()))
            ->count();

        // 未読メッセージ数
        $unreadMessages = Message::whereHas('room.roomUsers', fn ($q) =>
                $q->where('user_id', $userId)->where('user_type', UserType::MEMBER)
            )
            ->where('user_id', '!=', $userId)
            ->whereDoesntHave('messageReads', fn ($q) =>
                $q->where('user_id', $userId)->where('user_type', UserType::MEMBER)
            )
            ->count();

        return response()->json([
            'chat_invites'    => $chatInvites,
            'unread_messages' => $unreadMessages,
            'total'           => $chatInvites + $unreadMessages,
            // 将来追加しやすいように
            // 'announcements' => 0,
        ]);
    }
}