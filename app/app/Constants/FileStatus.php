<?php

declare(strict_types=1);

namespace App\Constants;

class FileStatus
{
    const CREATE    = 'create';
    const UPDATE    = 'update';
    const DELETE    = 'delete';

    const LABELS = [
        self::CREATE    => '新規',
        self::UPDATE    => '更新',
        self::DELETE    => '削除',
    ];

}