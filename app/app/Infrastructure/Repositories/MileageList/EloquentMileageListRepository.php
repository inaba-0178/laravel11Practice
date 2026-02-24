<?php
namespace App\Infrastructure\Repositories\MileageList; 

use App\Domain\MileageList\Entities\Mileage;
use App\Domain\MileageList\Repositories\MileageRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstMileageLists;
use App\Infrastructure\Repositories\BaseRepository;

class EloquentMileageListRepository extends BaseRepository implements MileageRepositoryInterface
{
    public function __construct(MstMileageLists $model)
    {
        parent::__construct($model);
    }

    public function findActive(): array
    {
        $mileages = $this->model
            ->get();

        return $this->toEntities($mileages, fn($model) => $this->toEntity($model));
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
}