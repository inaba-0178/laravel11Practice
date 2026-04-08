<?php

namespace App\Domain\FavoriteCars\Repositories;

use Illuminate\Support\Collection;

interface FavoriteCarsRepositoryInterface
{
    /**
     * car_idの配列で車両情報を一括取得
     *
     * @param  array $carIds
     * @return Collection
     */
    public function findByCarIds(array $carIds): Collection;

    /**
     * car_idの配列でメイン画像を一括取得
     *
     * @param  array $carIds
     * @return Collection
     */
    public function findMainImagesByCarIds(array $carIds): Collection;
}