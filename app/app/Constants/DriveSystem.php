<?php

declare(strict_types=1);

namespace App\Constants;

class DriveSystem
{
    const TWO_WD    = '2WD';
    const FOUR_WD   = '4WD';
    const AWD       = 'AWD';
    const FR        = 'FR';
    const FF        = 'FF';
    const MR        = 'MR';
    const RR        = 'RR';

    const LABELS = [
        self::TWO_WD    => '2WD',
        self::FOUR_WD   => '4WD',
        self::AWD       => 'AWD',
        self::FR        => 'FR',
        self::FF        => 'FF',
        self::MR        => 'MR',
        self::RR        => 'RR',
    ];
}