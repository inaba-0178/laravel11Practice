<?php

declare(strict_types=1);

namespace App\Constants;

class FeaturedBrandPosition
{
    const JP_TOP_ROW     = 'jp-top-row';
    const IMPORT_TOP_ROW = 'import-top-row';

    const LABELS = [
        self::JP_TOP_ROW     => '中古車',
        self::IMPORT_TOP_ROW => '輸入中古車',
    ];
}
