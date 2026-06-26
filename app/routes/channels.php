<?php

use App\Infrastructure\Eloquent\User\UsrUser;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('room.{roomId}', function ($user, $roomId) {
    if ($user instanceof UsrUser) {
        return $user->rooms()->where('rooms.id', $roomId)->exists()
            ? ['id' => $user->id, 'name' => $user->display_name]
            : null;
    }
    // Staff (admin) は全ルームにアクセス可
    return ['id' => $user->id, 'name' => $user->name];
});