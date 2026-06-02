<?php
namespace App\Infrastructure\Repositories\SelectBodyTypeList; 

use App\Domain\SelectBodyTypeList\Repositories\CarSerieRepositoryInterface;
use App\Domain\SelectBodyTypeList\Entities\CarSerie;
use App\Infrastructure\Eloquent\Mst\MstCarSeries;
use App\Infrastructure\Repositories\BaseRepository;

class EloquentCarSeriesRepository extends BaseRepository implements CarSerieRepositoryInterface
{

    public function __construct(MstCarSeries $model)
    {
        $this->model = $model;
    }

    public function findByCarSeries(array $seriesIds, array $conditions = []): array
    {
        $carSeries = $this->model
            ->whereIn('series_id', $seriesIds)
            ->get();

        return $carSeries->map(fn (MstCarSeries $carSerie) => $this->toEntity($carSerie))->all();
    }

    /**
     * EloquentモデルをEntityに変換
     */
    private function toEntity(MstCarSeries $model): CarSerie
    {
        $imageFilePath = '';
        if ($model->mainImage) {
            $imageFilePath = $this->buildS3Url('mst_car_series_images', $model->mainImage->file_path ?? '');
        }

        return new CarSerie(
            $model->series_id,
            $model->series_name,
            $model->manufacturer_id,
            $imageFilePath,
        );
    }
}