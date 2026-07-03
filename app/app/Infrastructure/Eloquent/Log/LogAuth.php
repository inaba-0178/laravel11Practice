<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Log;

use Illuminate\Database\Eloquent\Model;

class LogAuth extends Model
{
    protected $connection = 'log';
    protected $table      = 'log_auth';
    public    $timestamps = false;

    protected $fillable = [
        'operator_type',
        'operator_id',
        'operator_name',
        'action',
        'result',
        'ip_address',
        'user_agent',
        'operation_at',
    ];

    protected $casts = [
        'operation_at' => 'datetime',
        'created_at'   => 'datetime',
    ];
}
