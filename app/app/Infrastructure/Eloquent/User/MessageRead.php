<?php

namespace App\Infrastructure\Eloquent\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MessageRead extends Model
{
    use HasFactory;

    protected $connection = 'user';
    protected $table = 'message_reads';
    public $timestamps = false;

    protected $fillable = [
        'message_id',
        'user_id',
        'user_type',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function getSenderAttribute(): ?object
    {
        if ($this->user_type === 'staff') {
            return User::find($this->user_id);
        }
        return UsrUser::find($this->user_id);
    }
}