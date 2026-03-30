<?php

declare(strict_types=1);

namespace App\Constants;

class RepairHistory
{
    const NONE      = 'none';
    const MINOR     = 'minor';
    const MAJOR     = 'major';
    const UNKNOWN   = 'unknown';

    const LABELS = [
        self::NONE      => 'なし',
        self::MINOR     => '軽微あり',
        self::MAJOR     => 'あり',
        self::UNKNOWN   => '不明'
    ];
}