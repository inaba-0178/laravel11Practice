<?php
namespace App\Infrastructure\Repositories\SelectBodyTypeList; 

use App\Domain\SelectBodyTypeList\Repositories\CarSerieRepositoryInterface;
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
            ->get()
            ->toArray();
            
        return $carSeries;
    }
}