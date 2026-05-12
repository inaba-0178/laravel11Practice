<?php

declare(strict_types=1);

namespace App\Constants;

class NaviOption
{
    const NAVI     = 'navi';
    const TV       = 'tv';
    const DVD_NAVI = 'dvd_navi';

    const LABELS = [
        self::NAVI     => 'カーナビあり',
        self::TV       => 'TVあり',
        self::DVD_NAVI => 'DVDナビあり',
    ];
}