<?php

namespace App\Infrastructure\Eloquent\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'user_id',
        'message',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messageReads()
    {
        return $this->hasMany(MessageRead::class);
    }
}