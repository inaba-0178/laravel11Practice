<?php

namespace App\Presentation\Controllers\Message;

use App\Application\UseCases\Message\GetMessagesUseCase;
use App\Application\UseCases\Message\SendMessageUseCase;
use App\Application\UseCases\Message\ReadMessagesUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\UserTyping;

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

        $message = $this->sendMessageUseCase->execute(
            $roomId,
            $request->user()->id,
            $request->message
        );

        return response()->json($message, 201);
    }

    public function typing(Request $request, int $roomId)
    {
        broadcast(new UserTyping(
            roomId:   $roomId,
            userId:   $request->user()->id,
            userName: $request->user()->name,
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

        $this->readMessagesUseCase->execute(
            $request->message_ids,
            $request->user()->id
        );

        return response()->json(['status' => 'ok']);
    }
}