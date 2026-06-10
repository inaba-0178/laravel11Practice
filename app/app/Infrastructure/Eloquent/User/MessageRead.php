<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\User;

use App\Domain\Shared\Constants\UserType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageRead extends Model
{
    use HasFactory;

    protected $connection = 'user';
    protected $table      = 'message_reads';
    public    $timestamps = false;

    protected $fillable = [
        'message_id',
        'user_id',
        'user_type',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function getSenderAttribute(): ?object
    {
        return $this->user_type === UserType::STAFF
            ? User::find($this->user_id)
            : UsrUser::find($this->user_id);
    }
}