<?php
namespace App\Infrastructure\Repositories\RidingCapacityList; 

use App\Domain\RidingCapacityList\Entities\RidingCapacity;
use App\Domain\RidingCapacityList\Repositories\RidingCapacityRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstRidingCapacityLists;

class EloquentRidingCapacityListRepository implements RidingCapacityRepositoryInterface
{
    private MstRidingCapacityLists $model;

    public function __construct(MstRidingCapacityLists $model)
    {
        $this->model = $model;
    }

    public function findAll(): array
    {
        $ridingCapacityLists = $this->model
            ->get();

        return $this->toEntities($ridingCapacityLists);
    }

    public function findActive(): array
    {
        $ridingCapacities = $this->model
            ->get();

        return $this->toEntities($ridingCapacities);
    }

    public function findById(int $id): ?RidingCapacity
    {
        $ridingCapacity = $this->model
            ->find($id);

        if (!$ridingCapacity) {
            return null;
        }

        return $this->toEntity($ridingCapacity);
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param MstRidingCapacityLists
     * @return RidingCapacity
     */
    private function toEntity(MstRidingCapacityLists $model): RidingCapacity
    {
        return new RidingCapacity(
            $model->id,
            $model->name,
            $model->max_amount,
            $model->is_unlimited,
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