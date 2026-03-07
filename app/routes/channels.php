<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('room.{roomId}', function ($user, $roomId) {
    return $user->rooms()->where('room_id', $roomId)->exists()
        ? ['id' => $user->id, 'name' => $user->name]
        : null;
});