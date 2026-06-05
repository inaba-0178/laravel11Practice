<?php

declare(strict_types=1);

namespace App\Application\UseCases\DealerAnalytics;

/**
 * ディーラー分析データ取得ユースケースの出力データ
 */
final class GetDealerAnalyticsOutputData
{
    public function __construct(
        /** グラフ用ラベル（日付） */
        public readonly array $labels,

        /** 閲覧数データ */
        public readonly array $viewCounts,

        /** お気に入り数データ */
        public readonly array $favoriteCounts,

        /** 予約数データ */
        public readonly array $reservationCounts,

        /** 在庫確認・見積数データ */
        public readonly array $inquiryCounts,

        /** サマリー（合計） */
        public readonly array $summary,
    ) {}
}