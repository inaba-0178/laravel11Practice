<?php

declare(strict_types=1);

namespace App\Constants;

final class RoleConstants
{
    public const SUPER        = 'super';
    public const ADMIN        = 'admin';
    public const DEALER       = 'dealer';
    public const DEALER_STAFF = 'dealer_staff';

    public const HIERARCHY = [
        self::SUPER        => 4,
        self::ADMIN        => 3,
        self::DEALER       => 2,
        self::DEALER_STAFF => 1,
    ];

    public const LABELS = [
        self::SUPER        => 'スーパーユーザー',
        self::ADMIN        => '管理者',
        self::DEALER       => 'ディーラー',
        self::DEALER_STAFF => 'ディーラースタッフ',
    ];

    /**
     * 自分より低いロールの一覧を取得
     */
    public static function getLowerRoles(string $role): array
    {
        $myLevel = self::HIERARCHY[$role] ?? 0;

        return array_keys(array_filter(
            self::HIERARCHY,
            fn ($level) => $level < $myLevel
        ));
    }

    /**
     * 自分以下のロールの一覧を取得（自分含む）
     */
    public static function getLowerOrEqualRoles(string $role): array
    {
        $myLevel = self::HIERARCHY[$role] ?? 0;

        return array_keys(array_filter(
            self::HIERARCHY,
            fn ($level) => $level <= $myLevel
        ));
    }
}