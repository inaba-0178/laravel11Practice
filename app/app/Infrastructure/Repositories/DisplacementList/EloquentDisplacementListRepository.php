<?php
namespace App\Infrastructure\Repositories\DisplacementList; 

use App\Domain\DisplacementList\Entities\Displacement;
use App\Domain\DisplacementList\Repositories\DisplacementRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstDisplacementLists;
use App\Infrastructure\Repositories\BaseRepository;

class EloquentDisplacementListRepository extends BaseRepository implements DisplacementRepositoryInterface
{

    public function __construct(MstDisplacementLists $model)
    {
        parent::__construct($model);
    }

    public function findActive(): array
    {
        $displacements = $this->model
            ->get();

        return $this->toEntities($displacements, fn($model) => $this->toEntity($model));
        
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