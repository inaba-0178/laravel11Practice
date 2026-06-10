<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Message;

use App\Application\UseCases\Message\GetMessagesUseCase;
use App\Application\UseCases\Message\ReadMessagesUseCase;
use App\Application\UseCases\Message\SendMessageUseCase;
use App\Domain\Shared\Constants\UserType;
use App\Http\Controllers\Controller;
use App\Infrastructure\Eloquent\User\UsrUser;
use App\Infrastructure\Events\MessageRead;
use App\Infrastructure\Events\MessageSent;
use App\Infrastructure\Events\UserTyping;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(
        private readonly GetMessagesUseCase  $getMessagesUseCase,
        private readonly SendMessageUseCase  $sendMessageUseCase,
        private readonly ReadMessagesUseCase $readMessagesUseCase,
    ) {}

    public function index(int $roomId): JsonResponse
    {
        return response()->json(
            $this->getMessagesUseCase->execute($roomId)
        );
    }

    public function store(Request $request, int $roomId): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userId   = (string) $request->user()->id;
        $userType = $this->getUserType($request);

        $message = $this->sendMessageUseCase->execute(
            $roomId,
            $userId,
            $userType,
            $request->message
        );

        broadcast(new MessageSent(
            roomId:    $roomId,
            userId:    $userId,
            userType:  $userType,
            id:        $message->id,
            message:   $request->message,
            createdAt: $message->created_at->toISOString(),
            userModel: $request->user(),
        ));

        return response()->json($message, 201);
    }

    public function typing(Request $request, int $roomId): JsonResponse
    {
        $userId   = (string) $request->user()->id;
        $userType = $this->getUserType($request);
        $userName = UserType::getDisplayName($request->user(), $userType);

        broadcast(new UserTyping(
            roomId:   $roomId,
            userId:   $userId,
            userType: $userType,
            userName: $userName,
            isTyping: $request->boolean('is_typing'),
        ))->toOthers();

        return response()->json(['status' => 'ok']);
    }

    public function read(Request $request, int $roomId): JsonResponse
    {
        $request->validate([
            'message_ids'   => 'required|array',
            'message_ids.*' => 'exists:messages,id',
        ]);

        $userId   = (string) $request->user()->id;
        $userType = $this->getUserType($request);

        $this->readMessagesUseCase->execute(
            $request->message_ids,
            $userId,
            $userType,
        );

        broadcast(new MessageRead(
            roomId:     $roomId,
            userId:     $userId,
            userType:   $userType,
            messageIds: $request->message_ids,
        ));

        return response()->json(['status' => 'ok']);
    }

    private function getUserType(Request $request): string
    {
        return $request->user() instanceof UsrUser
            ? UserType::MEMBER
            : UserType::STAFF;
    }
}