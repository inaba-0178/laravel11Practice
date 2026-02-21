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

    public function findActive(): array
    {
        $displacements = $this->model
            ->get();

        return $displacements->map(fn (MstDisplacementLists $displacement) => $this->toEntity($displacement))->all();
        
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param   MstDisplacementLists
     * @return  Displacement
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

}