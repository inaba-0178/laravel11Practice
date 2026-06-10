<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\User;

use App\Domain\Shared\Constants\UserType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    use HasFactory;

    protected $connection = 'user';
    protected $table      = 'messages';

    protected $fillable = [
        'room_id',
        'user_id',
        'user_type',
        'message',
        'attachment_url',
        'attachment_type',
        'attachment_name',
        'attachment_size',
    ];
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function messageReads(): HasMany
    {
        return $this->hasMany(MessageRead::class);
    }

    public function getSenderAttribute(): ?object
    {
        return $this->user_type === UserType::STAFF
            ? User::find($this->user_id)
            : UsrUser::find($this->user_id);
    }
}