<?php

declare(strict_types=1);

namespace App\Constants;

class ImageType
{
    const EXTERIOR = 'exterior';
    const INTERIOR = 'interior';
    const ENGINE   = 'engine';
    const OTHER    = 'other';

    const LABELS = [
        self::EXTERIOR => 'エクステリア',
        self::INTERIOR => 'インテリア',
        self::ENGINE   => 'エンジン',
        self::OTHER    => 'その他',
    ];
}