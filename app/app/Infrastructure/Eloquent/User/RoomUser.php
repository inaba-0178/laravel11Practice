<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\User;

use App\Domain\Shared\Constants\UserType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomUser extends Model
{
    use HasFactory;

    protected $connection = 'user';
    protected $table      = 'room_users';

    protected $fillable = [
        'room_id',
        'user_id',
        'user_type',
        'status',
        'invited_at',
        'responded_at',
        'expired_at',
    ];

    protected $casts = [
        'invited_at'   => 'datetime',
        'responded_at' => 'datetime',
        'expired_at'   => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function getSenderAttribute(): ?object
    {
        return $this->user_type === UserType::STAFF
            ? User::find($this->user_id)
            : UsrUser::find($this->user_id);
    }
}