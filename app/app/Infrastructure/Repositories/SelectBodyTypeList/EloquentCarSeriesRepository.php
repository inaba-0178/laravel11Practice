<?php
namespace App\Infrastructure\Repositories\SelectBodyTypeList; 

use App\Domain\SelectBodyTypeList\Repositories\CarSerieRepositoryInterface;
use App\Domain\SelectBodyTypeList\Entities\CarSerie;
use App\Infrastructure\Eloquent\Mst\MstCarSeries;

class EloquentCarSeriesRepository implements CarSerieRepositoryInterface
{
    private MstCarSeries $model;

    public function __construct(MstCarSeries $model)
    {
        $this->model = $model;
    }

    public function findByCarSeries(array $seriesIds, array $conditions = []): array
    {
        $carSeries = $this->model
            ->whereIn('series_id', $seriesIds)
            ->get();
            
        return $this->toEntities($carSeries);
    }

    /**
     * EloquentモデルをEntityに変換
     */
    private function toEntity(MstCarSeries $model): CarSerie
    {
        return new CarSerie(
            $model->series_id,
            $model->series_name,
            $model->manufacturer_id, // ← ここは実際のカラム名に合わせてください
        );
    }

    /**
     * Eloquentコレクションをエンティティ配列に変換
     */
    private function toEntities($models): array
    {
        return $models->map(function ($model) {
            return $this->toEntity($model);
        })->all();
    }
}