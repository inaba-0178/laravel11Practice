<?php

declare(strict_types=1);

namespace App\Application\UseCases\DealerAnalytics;

/**
 * ディーラー分析データ取得ユースケースへの入力データ
 */
final class GetDealerAnalyticsInputData
{
    public function __construct(
        /** ディーラーID */
        public readonly int $dealerId,

        /** 期間種別（weekly/monthly/yearly） */
        public readonly string $period,

        /** 基準日（この日から遡る） */
        public readonly \Carbon\Carbon $baseDate,
    ) {}
}