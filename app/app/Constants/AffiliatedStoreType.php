<?php

declare(strict_types=1);

namespace App\Constants;

final class AffiliatedStoreType
{
    public const AFFILIATED = 'affiliated';
    public const PARTNER    = 'partner';

    public const LABELS = [
        self::AFFILIATED => '系列店',
        self::PARTNER    => '提携店',
    ];

    public const COLORS = [
        self::AFFILIATED => 'info',
        self::PARTNER    => 'success',
    ];

    public static function label(string $type): string
    {
        return self::LABELS[$type] ?? '-';
    }

    public static function color(string $type): string
    {
        return self::COLORS[$type] ?? 'gray';
    }
}