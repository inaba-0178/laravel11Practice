<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;

class RoomUserDeletionLog extends Model
{
    protected $connection = 'user';
    protected $table      = 'room_user_deletion_logs';

    protected $fillable = [
        'room_id',
        'deleted_user_id',
        'deleted_user_type',
        'deleted_by_id',
        'deleted_by_type',
        'reason',
        'reason_detail',
    ];
}