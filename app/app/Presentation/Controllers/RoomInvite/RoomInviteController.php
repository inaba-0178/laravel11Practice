<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\RoomInvite;

use App\Application\UseCases\RoomInvite\ApproveInviteUseCase;
use App\Application\UseCases\RoomInvite\GetPendingInvitesUseCase;
use App\Application\UseCases\RoomInvite\RejectInviteUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomInviteController extends Controller
{
    public function __construct(
        private readonly GetPendingInvitesUseCase $getPendingInvitesUseCase,
        private readonly ApproveInviteUseCase     $approveInviteUseCase,
        private readonly RejectInviteUseCase      $rejectInviteUseCase,
    ) {}

    public function pending(Request $request): JsonResponse
    {
        $invites = $this->getPendingInvitesUseCase->execute(
            (string) $request->user()->id
        );

        return response()->json($invites);
    }

    public function approve(Request $request, int $roomId): JsonResponse
    {
        try {
            $roomId = $this->approveInviteUseCase->execute(
                $roomId,
                (string) $request->user()->id
            );

            return response()->json([
                'message' => '参加しました',
                'room_id' => $roomId,
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function reject(Request $request, int $roomId): JsonResponse
    {
        try {
            $this->rejectInviteUseCase->execute(
                $roomId,
                (string) $request->user()->id
            );

            return response()->json(['message' => '拒否しました']);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}