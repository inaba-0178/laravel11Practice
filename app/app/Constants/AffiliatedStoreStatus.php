<?php

declare(strict_types=1);

namespace App\Constants;

final class AffiliatedStoreStatus
{
    public const PENDING   = 'pending';
    public const APPROVED  = 'approved';
    public const REJECTED  = 'rejected';
    public const DISSOLVED = 'dissolved';

    public const LABELS = [
        self::PENDING   => '申請中',
        self::APPROVED  => '承認済み',
        self::REJECTED  => '拒否',
        self::DISSOLVED => '解除済み',
    ];

    public const COLORS = [
        self::PENDING   => 'warning',
        self::APPROVED  => 'success',
        self::REJECTED  => 'danger',
        self::DISSOLVED => 'gray',
    ];

    public static function label(string $status): string
    {
        return self::LABELS[$status] ?? '-';
    }

    public static function color(string $status): string
    {
        return self::COLORS[$status] ?? 'gray';
    }

    public static function isActive(string $status): bool
    {
        return in_array($status, [self::PENDING, self::APPROVED]);
    }
}