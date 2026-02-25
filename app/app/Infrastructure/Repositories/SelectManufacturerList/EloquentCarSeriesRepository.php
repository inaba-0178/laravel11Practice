<?php
namespace App\Infrastructure\Repositories\SelectManufacturerList; 

use App\Domain\SelectManufacturerList\Entities\CarSerie;
use App\Domain\SelectManufacturerList\Repositories\CarSerieRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstCarSeries;
use App\Infrastructure\Repositories\BaseRepository;

class EloquentCarSeriesRepository extends BaseRepository implements CarSerieRepositoryInterface
{

    public function __construct(MstCarSeries $model)
    {
        parent::__construct($model);
    }

    public function findByManufacturerId(int $manufacturerId, array $conditions = []): array
    {
        $carSeries = $this->model
            ->where('manufacturer_id', $manufacturerId)
            ->get();
        return $this->toEntities($carSeries, fn($model) => $this->toEntity($model));
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
}