<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Message;

use App\Application\Services\AttachmentService;
use App\Application\UseCases\Message\GetMessagesUseCase;
use App\Application\UseCases\Message\ReadMessagesUseCase;
use App\Application\UseCases\Message\SendMessageUseCase;
use App\Domain\Shared\Constants\AttachmentLimits;
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
        private readonly AttachmentService  $attachmentService,
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
            'message'     => 'nullable|string|max:1000',
            'attachments' => 'nullable|array|max:' . AttachmentLimits::MAX_FILES,
            'attachments.*' => 'file',
        ]);

        // メッセージか添付ファイルのどちらかは必須
        if (empty($request->message) && empty($request->file('attachments'))) {
            return response()->json(['message' => 'メッセージまたはファイルを入力してください'], 422);
        }

        $userId   = (string) $request->user()->id;
        $userType = $this->getUserType($request);

        $attachments = [];
        foreach ($request->file('attachments') ?? [] as $file) {
            $error = $this->attachmentService->validate($file);
            if ($error) {
                return response()->json(['message' => $error], 422);
            }
            $attachments[] = $this->attachmentService->upload($file, $roomId);
        }

        // 添付ファイルがある場合は1ファイル1メッセージで送信
        if (!empty($attachments)) {
            $messages = [];
            foreach ($attachments as $attachment) {
                $message = $this->sendMessageUseCase->execute(
                    $roomId,
                    $userId,
                    $userType,
                    $request->message ?? '',
                    $attachment,
                );

                broadcast(new MessageSent(
                    roomId:    $roomId,
                    userId:    $userId,
                    userType:  $userType,
                    id:        $message->id,
                    message:   $request->message ?? '',
                    createdAt: $message->created_at->toISOString(),
                    userModel: $request->user(),
                    attachment: $attachment,
                ));

                $messages[] = $message;
            }
            return response()->json($messages, 201);
        }

        // テキストのみ
        $message = $this->sendMessageUseCase->execute(
            $roomId,
            $userId,
            $userType,
            $request->message,
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