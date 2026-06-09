<?php

namespace App\Presentation\Controllers\Message;

use App\Application\UseCases\Message\GetMessagesUseCase;
use App\Application\UseCases\Message\SendMessageUseCase;
use App\Application\UseCases\Message\ReadMessagesUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Infrastructure\Events\UserTyping;
use App\Infrastructure\Events\MessageSent;
use App\Infrastructure\Events\MessageRead;

class MessageController extends Controller
{
    public function __construct(
        private readonly GetMessagesUseCase  $getMessagesUseCase,
        private readonly SendMessageUseCase  $sendMessageUseCase,
        private readonly ReadMessagesUseCase $readMessagesUseCase,
    ) {}

    public function index(int $roomId)
    {
        $messages = $this->getMessagesUseCase->execute($roomId);
        return response()->json($messages);
    }

    public function store(Request $request, int $roomId)
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

    public function typing(Request $request, int $roomId)
    {
        $userType = $this->getUserType($request);
        $userId   = (string) $request->user()->id;
        $userName = $userType === 'staff'
            ? $request->user()->name
            : $request->user()->sei . $request->user()->mei;

        broadcast(new UserTyping(
            roomId:   $roomId,
            userId:   $userId,
            userType: $userType,
            userName: $userName,
            isTyping: $request->boolean('is_typing'),
        ))->toOthers();

        return response()->json(['status' => 'ok']);
    }

    public function read(Request $request, int $roomId)
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
        return $request->user() instanceof \App\Infrastructure\Eloquent\User\UsrUser
            ? 'member'
            : 'staff';
    }
}