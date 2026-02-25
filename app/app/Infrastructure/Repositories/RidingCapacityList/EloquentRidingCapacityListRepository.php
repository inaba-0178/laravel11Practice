<?php
namespace App\Infrastructure\Repositories\RidingCapacityList; 

use App\Domain\RidingCapacityList\Entities\RidingCapacity;
use App\Domain\RidingCapacityList\Repositories\RidingCapacityRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstRidingCapacityLists;
use App\Infrastructure\Repositories\BaseRepository;

class EloquentRidingCapacityListRepository extends BaseRepository implements RidingCapacityRepositoryInterface
{

    public function __construct(MstRidingCapacityLists $model)
    {
        parent::__construct($model);
    }

    public function findActive(): array
    {
        $ridingCapacities = $this->model
            ->get();
        
        return $this->toEntities($ridingCapacities, fn($model) => $this->toEntity($model));
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
}