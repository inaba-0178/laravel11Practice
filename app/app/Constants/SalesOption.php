<?php

declare(strict_types=1);

namespace App\Constants;

final class SalesOption
{
    public const QUALITY_CERT   = 'quality_cert';
    public const PURCHASE_PLAN  = 'purchase_plan';
    public const SENSOR_AFTER   = 'sensor_after';
    public const ONLINE_CONSULT = 'online_consult';

    public const LABELS = [
        self::QUALITY_CERT   => '車両品質評価書付き',
        self::PURCHASE_PLAN  => '購入プラン付き',
        self::SENSOR_AFTER   => 'アフター保証対象車',
        self::ONLINE_CONSULT => 'オンライン相談可',
    ];

    public static function label(string $value): string
    {
        return self::LABELS[$value] ?? $value;
    }

    const SALES_OPTIONS = [
        self::QUALITY_CERT,
        self::PURCHASE_PLAN,
        self::SENSOR_AFTER,
        self::ONLINE_CONSULT,
    ];

}