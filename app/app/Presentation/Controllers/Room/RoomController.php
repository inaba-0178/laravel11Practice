<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Room;

use App\Application\UseCases\Room\CreateRoomUseCase;
use App\Application\UseCases\Room\GetRoomUseCase;
use App\Application\UseCases\Room\GetRoomsUseCase;
use App\Domain\Shared\Constants\UserType;
use App\Http\Controllers\Controller;
use App\Infrastructure\Eloquent\User\UsrUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function __construct(
        private readonly GetRoomsUseCase   $getRoomsUseCase,
        private readonly CreateRoomUseCase $createRoomUseCase,
        private readonly GetRoomUseCase    $getRoomUseCase,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $rooms = $this->getRoomsUseCase->execute(
            (string) $request->user()->id,
            $this->getUserType($request),
        );

        return response()->json($rooms);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'              => 'nullable|string|max:255',
            'type'              => 'required|in:direct,group',
            'users'             => 'required|array',
            'users.*.id'        => 'required|string',
            'users.*.user_type' => 'required|in:staff,member',
        ]);

        $room = $this->createRoomUseCase->execute(
            $request->only(['name', 'type', 'users']),
            (string) $request->user()->id,
            $this->getUserType($request),
        );

        return response()->json($room, 201);
    }

    public function show(int $roomId): JsonResponse
    {
        return response()->json(
            $this->getRoomUseCase->execute($roomId)
        );
    }

    private function getUserType(Request $request): string
    {
        return $request->user() instanceof UsrUser
            ? UserType::MEMBER
            : UserType::STAFF;
    }
}