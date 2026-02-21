<?php
namespace App\Infrastructure\Repositories\AreaCarList; 

use App\Domain\AreaCarList\Entities\Area;
use App\Domain\AreaCarList\Repositories\AreaRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstAreas;

class EloquentAreaRepository implements AreaRepositoryInterface
{
    private MstAreas $model;

    public function __construct(MstAreas $model)
    {
        $this->model = $model;
    }

    public function findAll(): array
    {
        $areas = $this->model
            ->orderBy('sort_order')
            ->get();

        return $areas->map(fn (MstAreas $area) => $this->toEntity($area))->all();
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param MstAreas
     * @return Area
     */
    private function toEntity(MstAreas $model): Area
    {
        return new Area(
            $model->id,
            $model->name,
            $model->sort_order
        );
    }

}