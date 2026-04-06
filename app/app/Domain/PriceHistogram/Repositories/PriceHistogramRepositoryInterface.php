<?php

namespace App\Domain\PriceHistogram\Repositories;

use App\Domain\PriceHistogram\ValueObjects\PriceHistogramCondition;

interface PriceHistogramRepositoryInterface
{
    /**
     * 価格帯ごとの車両件数を取得する
     *
     * @param  int $step 価格帯の刻み幅（万円）
     * @param  PriceHistogramCondition $condition 検索条件
     * @return array
     */
    public function getHistogram(int $step, PriceHistogramCondition $condition): array;
}