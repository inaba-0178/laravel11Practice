<?php

declare(strict_types=1);

namespace App\Constants;

class SpecialTypeOption
{
    const ONE_OWNER     = 'one_owner';
    const CAMPING_CAR   = 'camping_car';
    const WELFARE_CAR   = 'welfare_car';
    const UNUSED        = 'unused';
    const ECO_CAR       = 'eco_car';
    const UNREGISTERED  = 'unregistered';

    const LABELS = [
        self::ONE_OWNER     => 'ワンオーナー',
        self::CAMPING_CAR   => 'キャンピングカー',
        self::WELFARE_CAR   => '福祉車両',
        self::UNUSED        => '登録済未使用車',
        self::ECO_CAR       => 'エコカー減税対象',
        self::UNREGISTERED  => '未登録車',
    ];

    // その他オプション一覧から除外する値
    const EXCLUDE_FROM_OTHER_OPTIONS = [
        self::ONE_OWNER,
        self::CAMPING_CAR,
        self::WELFARE_CAR,
        self::UNUSED,
        self::ECO_CAR,
        self::UNREGISTERED,
    ];

    public static function label(string $value): string
    {
        return self::LABELS[$value] ?? $value;
    }

}