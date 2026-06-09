<?php

namespace App\Infrastructure\Eloquent\User;

use App\Models\User;
use App\Infrastructure\Eloquent\User\UsrUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;

    protected $connection = 'user';
    protected $table = 'rooms';

    protected $fillable = [
        'name',
        'type',
        'related_type',
        'related_id',
        'is_active',
    ];

    public function roomUsers()
    {
        return $this->hasMany(RoomUser::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'room_users', 'room_id', 'user_id')
            ->wherePivot('user_type', 'staff');
    }

    public function members()
    {
        return $this->belongsToMany(UsrUser::class, 'room_users', 'room_id', 'user_id')
            ->wherePivot('user_type', 'member');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // 全参加者を取得するヘルパー
    public function getAllParticipants(): array
    {
        $staff   = $this->users->map(fn($u) => [
            'id'        => (string) $u->id,
            'name'      => $u->name,
            'user_type' => 'staff',
        ]);

        $members = $this->members->map(fn($u) => [
            'id'        => (string) $u->id,
            'name'      => $u->sei . $u->mei,
            'user_type' => 'member',
        ]);

        return $staff->merge($members)->values()->toArray();
    }
}