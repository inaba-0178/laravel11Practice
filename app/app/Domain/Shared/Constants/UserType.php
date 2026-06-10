<?php

declare(strict_types=1);

namespace App\Domain\Shared\Constants;

final class UserType
{
    public const STAFF  = 'staff';
    public const MEMBER = 'member';

    public static function getRole(string $userType, ?string $role = null): string
    {
        if ($userType === self::MEMBER) return 'member';
        return $role ?? 'dealer';
    }

    public static function getDisplayName(object $user, string $userType): string
    {
        if ($userType === self::STAFF) {
            return $user->name ?? '不明';
        }
        return $user->display_name ?? '不明';
    }
}