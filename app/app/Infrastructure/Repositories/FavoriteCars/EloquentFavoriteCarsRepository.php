<?php

namespace App\Infrastructure\Repositories\FavoriteCars;

use App\Domain\FavoriteCars\Repositories\FavoriteCarsRepositoryInterface;
use App\Infrastructure\Eloquent\User\StkCar;
use App\Infrastructure\Eloquent\User\StkCarImages;
use Illuminate\Support\Collection;

class EloquentFavoriteCarsRepository implements FavoriteCarsRepositoryInterface
{
    /**
     * car_idの配列で車両情報を一括取得
     */
    public function findByCarIds(array $carIds): Collection
    {
        return StkCar::whereIn('id', $carIds)
            ->select([
                'id',
                'series_id',
                'price',
                'model_year',
                'mileage',
                'color',
                'status',
                'repair_history',
                'main_image_url',
                'fuel_type',
                'transmission',
            ])
            ->get();
    }

    /**
     * car_idの配列でメイン画像を一括取得
     */
    public function findMainImagesByCarIds(array $carIds): Collection
    {
        return StkCarImages::whereIn('car_id', $carIds)
            ->where('is_main', 1)
            ->select(['car_id', 'image_url'])
            ->get()
            ->keyBy('car_id');
    }
}