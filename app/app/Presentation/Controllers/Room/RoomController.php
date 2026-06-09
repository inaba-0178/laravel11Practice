<?php

namespace App\Presentation\Controllers\Room;

use App\Application\UseCases\Room\GetRoomsUseCase;
use App\Application\UseCases\Room\CreateRoomUseCase;
use App\Application\UseCases\Room\GetRoomUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function __construct(
        private readonly GetRoomsUseCase   $getRoomsUseCase,
        private readonly CreateRoomUseCase $createRoomUseCase,
        private readonly GetRoomUseCase    $getRoomUseCase,
    ) {}

    public function index(Request $request)
    {
        $userId   = (string) $request->user()->id;
        $userType = $this->getUserType($request);

        $rooms = $this->getRoomsUseCase->execute($userId, $userType);
        return response()->json($rooms);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'nullable|string|max:255',
            'type'     => 'required|in:direct,group',
            'users'    => 'required|array',
            'users.*.id'        => 'required|string',
            'users.*.user_type' => 'required|in:staff,member',
        ]);

        $authUserId   = (string) $request->user()->id;
        $authUserType = $this->getUserType($request);

        $room = $this->createRoomUseCase->execute(
            $request->only(['name', 'type', 'users']),
            $authUserId,
            $authUserType,
        );

        return response()->json($room, 201);
    }

    public function show(int $roomId)
    {
        $room = $this->getRoomUseCase->execute($roomId);
        return response()->json($room);
    }

    private function getUserType(Request $request): string
    {
        return $request->user() instanceof \App\Infrastructure\Eloquent\User\UsrUser
            ? 'member'
            : 'staff';
    }
}