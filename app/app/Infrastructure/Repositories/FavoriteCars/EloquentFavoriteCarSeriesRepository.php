<?php

namespace App\Infrastructure\Repositories\FavoriteCars;

use App\Domain\FavoriteCars\Repositories\FavoriteCarSeriesRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstCarSeries;

class EloquentFavoriteCarSeriesRepository implements FavoriteCarSeriesRepositoryInterface
{
    /**
     * series_idの配列でシリーズ名を一括取得
     */
    public function findSeriesNamesByIds(array $seriesIds): array
    {
        return MstCarSeries::whereIn('series_id', $seriesIds)
            ->pluck('series_name', 'series_id')
            ->toArray();
    }
}