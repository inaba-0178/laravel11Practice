<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'user_id',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}