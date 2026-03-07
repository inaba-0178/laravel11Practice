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
        private readonly GetRoomsUseCase  $getRoomsUseCase,
        private readonly CreateRoomUseCase $createRoomUseCase,
        private readonly GetRoomUseCase   $getRoomUseCase,
    ) {}

    public function index(Request $request)
    {
        $rooms = $this->getRoomsUseCase->execute($request->user()->id);
        return response()->json($rooms);
        // テスト用に固定ユーザーID
        // $userId = 1;
        // $rooms = $this->getRoomsUseCase->execute($userId);
        // return response()->json($rooms);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'nullable|string|max:255',
            'type'       => 'required|in:direct,group',
            'user_ids'   => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $room = $this->createRoomUseCase->execute(
            $request->only(['name', 'type', 'user_ids']),
            $request->user()->id
        );

        return response()->json($room, 201);
    }

    public function show(int $roomId)
    {
        $room = $this->getRoomUseCase->execute($roomId);
        return response()->json($room);
    }
}