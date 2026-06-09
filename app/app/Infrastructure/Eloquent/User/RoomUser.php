<?php

namespace App\Infrastructure\Eloquent\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomUser extends Model
{
    use HasFactory;

    protected $connection = 'user';
    protected $table = 'room_users';

    protected $fillable = [
        'room_id',
        'user_id',
        'user_type',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function getSenderAttribute(): ?object
    {
        if ($this->user_type === 'staff') {
            return User::find($this->user_id);
        }
        return UsrUser::find($this->user_id);
    }
}