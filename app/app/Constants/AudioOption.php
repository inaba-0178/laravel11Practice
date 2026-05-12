<?php

declare(strict_types=1);

namespace App\Constants;

class AudioOption
{
    const CD        = 'cd';
    const DVD       = 'dvd';
    const BLUETOOTH = 'bluetooth';
    const USB       = 'usb';

    const LABELS = [
        self::CD        => 'CD再生',
        self::DVD       => 'DVD再生',
        self::BLUETOOTH => 'Bluetooth',
        self::USB       => 'USB',
    ];
}