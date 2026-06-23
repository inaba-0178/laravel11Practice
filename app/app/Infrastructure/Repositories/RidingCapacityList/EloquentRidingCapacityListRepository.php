<?php
namespace App\Infrastructure\Repositories\RidingCapacityList; 

use App\Domain\RidingCapacityList\Entities\RidingCapacity;
use App\Domain\RidingCapacityList\Repositories\RidingCapacityRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstRidingCapacityLists;
use App\Infrastructure\Repositories\BaseRepository;
use App\Domain\Common\Constants\CacheConstants;
use Illuminate\Support\Facades\Cache;

class EloquentRidingCapacityListRepository extends BaseRepository implements RidingCapacityRepositoryInterface
{

    public function __construct(MstRidingCapacityLists $model)
    {
        parent::__construct($model);
    }

    public function findActive(): array
    {
        return Cache::remember(CacheConstants::KEY_RIDING_CAPACITY_LIST, CacheConstants::TTL_MST, function () {
            return $this->toEntities($this->model->get(), fn($model) => $this->toEntity($model));
        });
    }

    /**
     * EloquentモデルをEntityに変換
     *
     * @param MstRidingCapacityLists $model
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