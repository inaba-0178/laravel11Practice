<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Opr;

use Illuminate\Database\Eloquent\Model;

class OprPermissionLog extends Model
{
    protected $connection = 'mst';
    protected $table      = 'opr_permission_logs';
    public    $timestamps = false;

    protected $fillable = [
        'resource_key',
        'changed_by',
        'changed_by_role',
        'before_roles',
        'after_roles',
    ];

    protected $casts = [
        'before_roles' => 'array',
        'after_roles'  => 'array',
        'created_at'   => 'datetime',
    ];
}
