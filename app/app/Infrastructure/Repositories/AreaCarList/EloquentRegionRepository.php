<?php
namespace App\Infrastructure\Repositories\AreaCarList; 

use App\Domain\AreaCarList\Entities\Region;
use App\Domain\AreaCarList\Repositories\RegionRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstRegions;

class EloquentRegionRepository implements RegionRepositoryInterface
{
    private MstRegions $model;

    public function __construct(MstRegions $model)
    {
        $this->model = $model;
    }

    public function findAll(): array
    {
        $areas = $this->model
            ->orderBy('sort_order')
            ->get();

        return $areas->map(fn (MstRegions $area) => $this->toEntity($area))->all();
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param MstRegions
     * @return Region
     */
    private function toEntity(MstRegions $model): Region
    {
        return new Region(
            $model->id,
            $model->area_code,
            $model->name,
            $model->query_param,
            $model->sort_order,
        );
    }

}