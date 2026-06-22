<?php
namespace App\Infrastructure\Repositories\AreaCarList; 

use App\Domain\AreaCarList\Entities\Region;
use App\Domain\AreaCarList\Repositories\RegionRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstRegions;
use App\Domain\Common\Constants\CacheConstants;
use Illuminate\Support\Facades\Cache;

class EloquentRegionRepository implements RegionRepositoryInterface
{
    private MstRegions $model;

    public function __construct(MstRegions $model)
    {
        $this->model = $model;
    }

    public function findAll(): array
    {
        return Cache::remember(CacheConstants::KEY_REGIONS . ':area_car', CacheConstants::TTL_MST, function () {
            return $this->model
                ->orderBy('sort_order')
                ->get()
                ->map(fn (MstRegions $region) => $this->toEntity($region))
                ->all();
        });
    }

    /**
     * EloquentモデルをEntityに変換
     *
     * @param MstRegions $model
     * @return Region
     */
    private function toEntity(MstRegions $model): Region
    {
        return new Region(
            $model->id,
            $model->area_code,
            $model->name,
            $model->query_param,
            $model->sort_order,
        );
    }

}