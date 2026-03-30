<?php

declare(strict_types=1);

namespace App\Constants;

class SteeringWheel
{
    const RIGHT = 'right';
    const LEFT  = 'left';

    const LABELS = [
        self::RIGHT => '右ハンドル',
        self::LEFT  => '左ハンドル',
    ];
}