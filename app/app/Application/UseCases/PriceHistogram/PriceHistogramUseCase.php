<?php

namespace App\Application\UseCases\PriceHistogram;

use App\Domain\Common\Constants\PriceHistogramConstants;
use App\Domain\PriceHistogram\Repositories\PriceHistogramRepositoryInterface;
use App\Domain\PriceHistogram\ValueObjects\PriceHistogramCondition;
use Illuminate\Support\Facades\Cache;

class PriceHistogramUseCase
{
    public function __construct(
        private readonly PriceHistogramRepositoryInterface $repository,
    ) {}

    /**
     * 価格帯ごとの車両件数を取得する
     *
     * @return PriceHistogramOutputData
     */
    public function execute(PriceHistogramCondition $condition): PriceHistogramOutputData
    {
        // 条件をキャッシュキーに含めて条件ごとにキャッシュする
        $cacheKey = PriceHistogramConstants::CACHE_KEY . ':' . md5(serialize($condition));

        $histogram = Cache::remember(
            $cacheKey,
            PriceHistogramConstants::CACHE_TTL,
            fn () => $this->repository->getHistogram(PriceHistogramConstants::PRICE_STEP, $condition)
        );

        return new PriceHistogramOutputData($histogram);
    }
}