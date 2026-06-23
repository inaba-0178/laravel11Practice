<?php

namespace App\Infrastructure\Repositories\SeriesStkCount;

use App\Domain\SeriesStkCount\Repositories\SeriesStkCountRepositoryInterface;
use App\Domain\Common\Constants\CacheConstants;
use App\Infrastructure\Eloquent\User\StkCar;
use Illuminate\Support\Facades\Cache;

class EloquentSeriesStkCountRepository implements SeriesStkCountRepositoryInterface
{
    public function countBySeriesIds(array $seriesIds): array
    {
        $cacheKey = CacheConstants::KEY_SERIES_STK_COUNT . ':' . md5(serialize($seriesIds));
        return Cache::remember($cacheKey, CacheConstants::TTL_CAR, function () use ($seriesIds) {
            return StkCar::query()
                ->whereIn('series_id', $seriesIds)
                ->where('status', 'available')
                ->whereNull('deleted_at')
                ->selectRaw('series_id, COUNT(*) as num')
                ->groupBy('series_id')
                ->get()
                ->toArray();
        });
    }
}