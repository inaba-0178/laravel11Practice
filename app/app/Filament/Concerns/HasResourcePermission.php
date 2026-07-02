<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use App\Constants\RoleConstants;
use App\Domain\Common\Services\PermissionCacheService;

trait HasResourcePermission
{
    public static function shouldRegisterNavigation(): bool
    {
        if (!static::$shouldRegisterNavigation) {
            return false;
        }

        return static::canAccess();
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        // superは常にアクセス可能
        if ($user->role === RoleConstants::SUPER) {
            return true;
        }

        $allowedRoles = PermissionCacheService::getAllowedRoles(static::getPermissionKey());

        // DBに未登録の場合は親リソースの権限にフォールバック
        if ($allowedRoles === null) {
            $parentKey = static::getParentPermissionKey();
            if ($parentKey !== null) {
                $allowedRoles = PermissionCacheService::getAllowedRoles($parentKey);
                // 複数形フォールバック: MstAreaResource → MstAreasResource
                if ($allowedRoles === null && str_ends_with($parentKey, 'Resource')) {
                    $allowedRoles = PermissionCacheService::getAllowedRoles(
                        substr($parentKey, 0, -8) . 'sResource'
                    );
                }
            }
        }

        if ($allowedRoles === null) {
            return false;
        }

        return in_array($user->role, $allowedRoles, true);
    }

    /**
     * 各Resource/Pageのクラス名をキーとして使用
     * 必要であれば各クラスでオーバーライド可能
     */
    protected static function getPermissionKey(): string
    {
        return class_basename(static::class);
    }

    /**
     * 命名規則で親リソースキーを自動解決
     * MstBasicOptionDetail → MstBasicOptionResource
     * 必要であれば各クラスでオーバーライド可能
     */
    protected static function getParentPermissionKey(): ?string
    {
        $key = static::getPermissionKey();

        if (str_ends_with($key, 'Detail')) {
            return substr($key, 0, -6) . 'Resource';
        }

        if (str_ends_with($key, 'DetailPage')) {
            return substr($key, 0, -10) . 'Resource';
        }

        return null;
    }
}
