<?php
namespace App\Infrastructure\Repositories\SelectManufacturerList; 

use App\Domain\SelectManufacturerList\Entities\CarSerie;
use App\Domain\SelectManufacturerList\Repositories\CarSerieRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstCarSeries;

class EloquentCarSeriesRepository implements CarSerieRepositoryInterface
{
    private MstCarSeries $model;

    public function __construct(MstCarSeries $model)
    {
        $this->model = $model;
    }

    public function findAll(): array
    {
        $carSeries = $this->model
            ->get();

        return $this->toEntities($carSeries);
    }

    public function findActive(): array
    {
        $carSeries = $this->model
            ->get();

        return $this->toEntities($carSeries);
    }

    public function findById(int $id): ?CarSerie
    {
        $carSerie = $this->model
            ->find($id);

        if (!$carSerie) {
            return null;
        }

        return $this->toEntity($carSerie);
    }

    public function findByManufacturerId(int $manufacturerId, array $conditions = []): array
    {
        $carSeries = $this->model
            ->where('manufacturer_id', $manufacturerId)
            ->get();
            
        return $this->toEntities($carSeries);
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param MstCarSeries
     * @return CarSerie
     */
    private function toEntity(MstCarSeries $model): CarSerie
    {
        return new CarSerie(
            $model->series_id,
            $model->series_name,
            $model->manufacturer_id,
        );
    }

    /**
     * Eloquentコレクションをエンティティ配列に変換
     * 
     * @param  $models
     * @return array
     */
    private function toEntities($models): array
    {
        return $models->map(function ($model) {
            return $this->toEntity($model);
        })->all();
    }

}