<?php

declare(strict_types=1);

namespace App\Constants;

class InspectionStatus
{
    const AVAILABLE = 'available';
    const NONE      = 'none';
    const NEW_CAR   = 'new_car';

    const LABELS = [
        self::AVAILABLE => '車検あり',
        self::NONE      => '車検なし',
        self::NEW_CAR   => '新車',
    ];
}