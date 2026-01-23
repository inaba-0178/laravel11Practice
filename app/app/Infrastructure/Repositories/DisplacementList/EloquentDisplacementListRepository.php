<?php
namespace App\Infrastructure\Repositories\DisplacementList; 

use App\Domain\DisplacementList\Entities\Displacement;
use App\Domain\DisplacementList\Repositories\DisplacementRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstDisplacementLists;

class EloquentDisplacementListRepository implements DisplacementRepositoryInterface
{
    private MstDisplacementLists $model;

    public function __construct(MstDisplacementLists $model)
    {
        $this->model = $model;
    }

    public function findAll(): array
    {
        $displacementLists = $this->model
            ->get();

        return $this->toEntities($displacementLists);
    }

    public function findActive(): array
    {
        $displacements = $this->model
            ->get();

        return $this->toEntities($displacements);
    }

    public function findById(int $id): ?Displacement
    {
        $displacement = $this->model
            ->find($id);

        if (!$displacement) {
            return null;
        }

        return $this->toEntity($displacement);
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param MstDisplacementLists
     * @return Displacement
     */
    private function toEntity(MstDisplacementLists $model): Displacement
    {
        return new Displacement(
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