<?php
namespace App\Infrastructure\Repositories\GetRegionList; 

use App\Domain\GetRegionList\Entities\Region;
use App\Domain\GetRegionList\Entities\Area;
use App\Domain\GetRegionList\Repositories\RegionRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstRegions;
use App\Infrastructure\Eloquent\Mst\MstAreas;

class EloquentGetRegionListRepository implements RegionRepositoryInterface
{
    private MstRegions $model;

    public function __construct(MstRegions $model)
    {
        $this->model = $model;
    }

    public function findAll(): array
    {
        $regions = $this->model
            ->with('area')
            ->orderBy('sort_order')
            ->get();

        return $this->toEntities($regions);
    }

    //地方ごとにグルーピング
    public function findAllGroupedByArea(): array
    {
        $areas = MstAreas::query()
            ->with(['regions' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            }])
            ->orderBy('sort_order', 'asc')
            ->get();

        return $this->toAreaEntities($areas);
    }

    public function findActive(): array
    {
        $regions = $this->model
            ->with('area')
            ->orderBy('sort_order')
            ->get();

        return $this->toEntities($regions);
    }

    public function findById(int $id): ?Region
    {
        $region = $this->model
            ->with('area')
            ->find($id);

        if (!$region) {
            return null;
        }

        return $this->toEntity($region);
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param MstRegions
     * @return Region
     */
    private function toEntity(MstRegions $model): Region
    {
         // Area Entityを作成
        $area = null;
        if ($model->area) {
            $area = new Area(
                $model->area->id,
                $model->area->name,
                $model->area->sort_order,
                []
            );
        }

        return new Region(
            $model->id,
            $model->name,
            $model->query_param,
            $model->sort_order,
            $area
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

    /**
     * Areaエンティティへの変換
     * 
     * @param MstAreas
     * @return Area
    */
    private function toAreaEntity(MstAreas $model): Area
    {
        // 都道府県をRegionエンティティに変換
        $regions = $model->regions->map(function ($region) {
            return new Region(
                $region->id,
                $region->name,
                $region->query_param,
                $region->sort_order,
                null  // areaは含めない（循環参照防止）
            );
        })->all();

        return new Area(
            $model->id,
            $model->name,
            $model->sort_order,
            $regions
        );
    }

    private function toAreaEntities($models): array
    {
        return $models->map(fn($model) => $this->toAreaEntity($model))->all();
    }

}