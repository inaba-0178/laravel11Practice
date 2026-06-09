<?php

namespace App\Infrastructure\Eloquent\User;

use App\Models\User;
use App\Infrastructure\Eloquent\User\UsrUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $connection = 'user';
    protected $table = 'messages';

    protected $fillable = [
        'room_id',
        'user_id',
        'user_type',
        'message',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // user_typeに応じて適切なモデルを返す
    public function getSenderAttribute(): ?object
    {
        if ($this->user_type === 'staff') {
            return User::find($this->user_id);
        }
        return UsrUser::find($this->user_id);
    }

    // 後方互換性のためuserも残す
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function messageReads()
    {
        return $this->hasMany(MessageRead::class);
    }
}