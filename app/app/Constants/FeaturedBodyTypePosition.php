<?php

declare(strict_types=1);

namespace App\Constants;

class FeaturedBodyTypePosition
{
    const TOP_HI  = 'top-hi';
    const TOP_ROW = 'top-row';

    const LABELS = [
        self::TOP_HI  => '上段',
        self::TOP_ROW => '下段',
    ];
}
