<?php

declare(strict_types=1);

namespace App\Constants;

class InquiryStatus
{
    const NEW           = 'new';
    const DRAFT         = 'draft';
    const REPLIED       = 'replied';
    const PHONE_REPLIED = 'phone_replied';

    const LABELS = [
        self::NEW           => '新規',
        self::DRAFT         => '一時保存',
        self::REPLIED       => '返信済み',
        self::PHONE_REPLIED => '電話対応済み',
    ];

    const COLORS = [
        self::NEW           => 'warning',
        self::DRAFT         => 'info',
        self::REPLIED       => 'success',
        self::PHONE_REPLIED => 'primary',
    ];
}