<?php

declare(strict_types=1);

namespace App\Constants;

class Transmission
{
    const AT    = 'AT';
    const MT    = 'MT';
    const CVT   = 'CVT';
    const DCT   = 'DCT';
    const OTHER = 'other';

    const LABELS = [
        self::AT    => 'AT',
        self::MT    => 'MT',
        self::CVT   => 'CVT',
        self::DCT   => 'DCT',
        self::OTHER => 'その他',
    ];
}