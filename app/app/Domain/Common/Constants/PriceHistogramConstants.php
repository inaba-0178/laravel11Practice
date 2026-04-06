<?php

namespace App\Domain\Common\Constants;

class PriceHistogramConstants
{
    /** 価格帯の刻み幅（万円） */
    public const PRICE_STEP = 10;

    /** キャッシュ有効期限（秒） */
    public const CACHE_TTL = 3600;

    /** キャッシュキー */
    public const CACHE_KEY = 'price_histogram';

     /** 価格スライダーの表示上の最大値（万円） */
    public const PRICE_SLIDER_MAX = 2000;
}