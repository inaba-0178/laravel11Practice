<?php

declare(strict_types=1);

namespace App\Constants;

class SlideDoor
{
    const NONE          = 'none';
    const RIGHT_ONLY    = 'right_only';
    const BOTH_MANUAL   = 'both_manual';
    const BOTH_POWER    = 'both_power';
    const RIGHT_POWER   = 'right_power';
    const LEFT_POWER    = 'left_power';

    const LABELS = [
        self::NONE          => 'なし',
        self::RIGHT_ONLY    => '右のみ',
        self::BOTH_MANUAL   => '両側手動',
        self::BOTH_POWER    => '両側電動',
        self::RIGHT_POWER   => '右電動',
        self::LEFT_POWER    => '左電動',
    ];
}