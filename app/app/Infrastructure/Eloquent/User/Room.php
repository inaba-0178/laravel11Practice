<?php

namespace App\Infrastructure\Eloquent\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
    ];

    public function roomUsers()
    {
        return $this->hasMany(RoomUser::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'room_users');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}