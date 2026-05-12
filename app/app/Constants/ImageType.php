<?php

declare(strict_types=1);

namespace App\Constants;

class ImageType
{
    const EXTERIOR  = 'exterior';
    const INTERIOR  = 'interior';
    const ENGINE    = 'engine';
    const OTHER     = 'other';
    const PANORAMIC = '360';

    const LABELS = [
        self::EXTERIOR  => 'エクステリア',
        self::INTERIOR  => 'インテリア',
        self::ENGINE    => 'エンジン',
        self::OTHER     => 'その他',
        self::PANORAMIC => '360°画像',
    ];
}