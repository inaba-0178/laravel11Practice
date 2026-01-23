<?php
namespace App\Infrastructure\Repositories\MileageList; 

use App\Domain\MileageList\Entities\Mileage;
use App\Domain\MileageList\Repositories\MileageRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstMileageLists;

class EloquentMileageListRepository implements MileageRepositoryInterface
{
    private MstMileageLists $model;

    public function __construct(MstMileageLists $model)
    {
        $this->model = $model;
    }

    public function findAll(): array
    {
        $MileageLists = $this->model
            ->get();

        return $this->toEntities($MileageLists);
    }

    public function findActive(): array
    {
        $Mileages = $this->model
            ->get();

        return $this->toEntities($Mileages);
    }

    public function findById(int $id): ?Mileage
    {
        $Mileage = $this->model
            ->find($id);

        if (!$Mileage) {
            return null;
        }

        return $this->toEntity($Mileage);
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param MstMileageLists
     * @return Mileage
     */
    private function toEntity(MstMileageLists $model): Mileage
    {
        return new Mileage(
            $model->id,
            $model->name,
            $model->min_amount,
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