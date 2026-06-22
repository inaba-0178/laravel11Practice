<?php
namespace App\Infrastructure\Repositories\MileageList; 

use App\Domain\MileageList\Entities\Mileage;
use App\Domain\MileageList\Repositories\MileageRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstMileageLists;
use App\Infrastructure\Repositories\BaseRepository;
use App\Domain\Common\Constants\CacheConstants;
use Illuminate\Support\Facades\Cache;

class EloquentMileageListRepository extends BaseRepository implements MileageRepositoryInterface
{
    public function __construct(MstMileageLists $model)
    {
        parent::__construct($model);
    }

    public function findActive(): array
    {
        return Cache::remember(CacheConstants::KEY_MILEAGE_LIST, CacheConstants::TTL_MST, function () {
            return $this->toEntities($this->model->get(), fn($model) => $this->toEntity($model));
        });
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param MstMileageLists $model
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