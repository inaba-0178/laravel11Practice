<?php

declare(strict_types=1);

namespace App\Constants;

class LoanPlanLabel
{
    public const SYSTEM_DEFAULT = 'システムデフォルト';
    public const DEALER_PLAN    = 'ディーラープラン';
    public const DEFAULT_TYPE   = '通常ローン';

    public const TYPE_STANDARD = 'standard';
    public const TYPE_RESIDUAL = 'residual';

    public const TYPE_LABELS = [
        self::TYPE_STANDARD => '通常ローン',
        self::TYPE_RESIDUAL => '残価設定ローン',
    ];
}