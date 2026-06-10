<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\User;

use App\Domain\Shared\Constants\UserType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $connection = 'user';
    protected $table      = 'rooms';

    protected $fillable = [
        'name',
        'type',
        'related_type',
        'related_id',
        'is_active',
    ];

    public function roomUsers(): HasMany
    {
        return $this->hasMany(RoomUser::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'room_users', 'room_id', 'user_id')
            ->wherePivot('user_type', UserType::STAFF);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(UsrUser::class, 'room_users', 'room_id', 'user_id')
            ->wherePivot('user_type', UserType::MEMBER);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function getAllParticipants(): array
    {
        $staff = $this->users->map(fn ($u) => [
            'id'        => (string) $u->id,
            'name'      => $u->name,
            'user_type' => UserType::STAFF,
        ]);

        $members = $this->members->map(fn ($u) => [
            'id'        => (string) $u->id,
            'name'      => $u->display_name,
            'user_type' => UserType::MEMBER,
        ]);

        return $staff->merge($members)->values()->toArray();
    }
}