<?php

declare(strict_types=1);

namespace App\Domain\Common\Services;

use App\Constants\RoleConstants;
use App\Infrastructure\Eloquent\Opr\OprResourcePermission;
use Illuminate\Support\Facades\Cache;

class PermissionCacheService
{
    const CACHE_KEY = 'opr_resource_permissions';
    const CACHE_TTL = 3600;

    /**
     * 指定リソースの許可ロール一覧を返す
     * DBに定義がない場合は null（デフォルト拒否）
     */
    public static function getAllowedRoles(string $resourceKey): ?array
    {
        $all = static::loadAll();
        return $all[$resourceKey] ?? null;
    }

    /**
     * 全権限をキャッシュから取得（なければDBから）
     */
    public static function loadAll(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return OprResourcePermission::all()
                ->keyBy('resource_key')
                ->map(fn($p) => $p->allowed_roles)
                ->toArray();
        });
    }

    /**
     * キャッシュをクリア（権限変更時に呼ぶ）
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * super は常にアクセス可能
     */
    public static function isSuperUser(): bool
    {
        return auth()->user()?->role === RoleConstants::SUPER;
    }
}
