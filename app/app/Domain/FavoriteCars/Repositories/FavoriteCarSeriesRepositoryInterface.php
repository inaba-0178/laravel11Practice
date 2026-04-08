<?php

namespace App\Domain\FavoriteCars\Repositories;

interface FavoriteCarSeriesRepositoryInterface
{
    /**
     * series_idの配列でシリーズ名を一括取得
     *
     * @param  array $seriesIds
     * @return array ['series_id' => 'series_name']
     */
    public function findSeriesNamesByIds(array $seriesIds): array;
}