<?php

namespace App\Infrastructure\Repositories\SeriesStkCount;

use App\Domain\SeriesStkCount\Repositories\SeriesStkCountRepositoryInterface;
use App\Infrastructure\Eloquent\User\StkCar;

class EloquentSeriesStkCountRepository implements SeriesStkCountRepositoryInterface
{
    public function countBySeriesIds(array $seriesIds): array
    {
        return StkCar::query()
            ->whereIn('series_id', $seriesIds)
            ->where('status', 'available')
            ->whereNull('deleted_at')
            ->selectRaw('series_id, COUNT(*) as num')
            ->groupBy('series_id')
            ->get()
            ->toArray();
    }
}