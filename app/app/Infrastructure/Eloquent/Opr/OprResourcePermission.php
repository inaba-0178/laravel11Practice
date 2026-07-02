<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Opr;

use App\Domain\Common\Services\PermissionCacheService;
use Illuminate\Database\Eloquent\Model;

class OprResourcePermission extends Model
{
    protected $connection = 'mst';
    protected $table      = 'opr_resource_permissions';

    protected $fillable = [
        'resource_key',
        'resource_label',
        'resource_group',
        'allowed_roles',
    ];

    protected $casts = [
        'allowed_roles' => 'array',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => PermissionCacheService::clearCache());
        static::deleted(fn () => PermissionCacheService::clearCache());
    }
}
