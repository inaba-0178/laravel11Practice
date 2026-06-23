<?php
namespace App\Infrastructure\Repositories\DisplacementList; 

use App\Domain\DisplacementList\Entities\Displacement;
use App\Domain\DisplacementList\Repositories\DisplacementRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstDisplacementLists;
use App\Infrastructure\Repositories\BaseRepository;
use App\Domain\Common\Constants\CacheConstants;
use Illuminate\Support\Facades\Cache;

class EloquentDisplacementListRepository extends BaseRepository implements DisplacementRepositoryInterface
{

    public function __construct(MstDisplacementLists $model)
    {
        parent::__construct($model);
    }

    public function findActive(): array
    {
        return Cache::remember(CacheConstants::KEY_DISPLACEMENT_LIST, CacheConstants::TTL_MST, function () {
            return $this->toEntities($this->model->get(), fn($model) => $this->toEntity($model));
        });
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param   MstDisplacementLists $model
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