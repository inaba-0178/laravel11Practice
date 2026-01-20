<?php
namespace App\Infrastructure\Repositories\GetRegionList; 

use App\Domain\GetRegionList\Entities\Region;
use App\Domain\GetRegionList\Repositories\RegionRepositoryInterface;
use App\Models\Mst\MstRegions;

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
            ->orderBy('sort_order')
            ->get();

        return $this->toEntities($regions);
    }

    public function findActive(): array
    {
        $regions = $this->model
            ->orderBy('sort_order')
            ->get();

        return $this->toEntities($regions);
    }

    public function findById(int $id): ?Region
    {
        $region = $this->model->find($id);

        if (!$region) {
            return null;
        }

        return $this->toEntity($region);
    }

    /**
     * EloquentモデルをEntityに変換
     */
    private function toEntity(MstRegions $model): Region
    {
        return new Region(
            $model->id,
            $model->name,
            $model->query_param,
            $model->sort_order,
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