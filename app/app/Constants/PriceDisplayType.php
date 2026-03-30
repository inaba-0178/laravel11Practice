<?php

declare(strict_types=1);

namespace App\Constants;

class PriceDisplayType
{
    const ACTUAL        = 'actual';
    const NEGOTIABLE    = 'negotiable';
    const ASK           = 'ask';

    const LABELS = [
        self::ACTUAL        => '表示価格',
        self::NEGOTIABLE    => '応相談',
        self::ASK           => 'お問い合わせ',
    ];
}