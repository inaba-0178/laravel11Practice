<?php

namespace App\Application\UseCases\FavoriteCars;

use App\Domain\FavoriteCars\Repositories\FavoriteCarsRepositoryInterface;
use App\Domain\FavoriteCars\Repositories\FavoriteCarSeriesRepositoryInterface;
use App\Domain\FavoriteCars\ValueObjects\CarIds;

class FavoriteCarsUseCase
{
    public function __construct(
        private readonly FavoriteCarsRepositoryInterface      $carsRepository,
        private readonly FavoriteCarSeriesRepositoryInterface $seriesRepository,
    ) {}

    /**
     * お気に入り車両情報を一括取得
     *
     * @return FavoriteCarsOutputData
     */
    public function execute(CarIds $carIds): FavoriteCarsOutputData
    {
        $ids = $carIds->getValues();

        // 車両情報取得
        $cars = $this->carsRepository->findByCarIds($ids);

        // メイン画像取得
        $mainImages = $this->carsRepository->findMainImagesByCarIds($ids);

        // series_idを収集してシリーズ名を一括取得
        $seriesIds   = $cars->pluck('series_id')->unique()->toArray();
        $seriesNames = $this->seriesRepository->findSeriesNamesByIds($seriesIds);

        // CarIdsの順番でソート
        $sortedIds = array_flip($ids);
        $cars = $cars
            ->map(fn ($car) => [
                'id'             => $car->id,
                'series_name'    => $seriesNames[$car->series_id] ?? null,
                'price'          => $car->price,
                'model_year'     => $car->model_year,
                'mileage'        => $car->mileage,
                'color'          => $car->color,
                'status'         => $car->status,
                'repair_history' => $car->repair_history,
                'fuel_type'      => $car->fuel_type,
                'transmission'   => $car->transmission,
                'main_image_url' => $mainImages[$car->id]->image_url ?? null,
            ])
            ->sortBy(fn ($car) => $sortedIds[$car['id']] ?? PHP_INT_MAX)
            ->values();

        return new FavoriteCarsOutputData($cars);
    }
}