<?php

declare(strict_types=1);

namespace App\Constants;

class FuelType
{
    const GASOLINE  = 'gasoline';
    const DIESEL    = 'diesel';
    const HYBRID    = 'hybrid';
    const ELECTRIC  = 'electric';
    const PHEV      = 'phev';
    const OTHER     = 'other';

    const LABELS = [
        self::GASOLINE  => 'ガソリン',
        self::DIESEL    => 'ディーゼル',
        self::HYBRID    => 'ハイブリッド',
        self::ELECTRIC  => '電気',
        self::PHEV      => 'PHEV',
        self::OTHER     => 'その他',
    ];
}