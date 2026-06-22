<?php
namespace App\Infrastructure\Repositories\FeaturedBrandList; 

use App\Domain\FeaturedBrandList\Entities\Manufacturer;
use App\Domain\FeaturedBrandList\Repositories\ManufacturerRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstManufacturers;
use App\Infrastructure\Repositories\BaseRepository;
use App\Domain\Common\Constants\CacheConstants;
use Illuminate\Support\Facades\Cache;

class EloquentManufacturersRepository extends BaseRepository implements ManufacturerRepositoryInterface
{

    public function __construct(MstManufacturers $model)
    {
        parent::__construct($model);
    }

    public function findByCodes(array $codes, array $conditions = []): array
    {
        $cacheKey = CacheConstants::KEY_MANUFACTURERS . ':codes:' . md5(serialize($codes));
        return Cache::remember($cacheKey, CacheConstants::TTL_MST, function () use ($codes) {
            return $this->toEntities(
                $this->model->whereIn('code', $codes)->orderBy('sort_order')->get(),
                fn($model) => $this->toEntity($model)
            );
        });
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param MstManufacturers
     * @return Manufacturer
     */
    private function toEntity(MstManufacturers $model): Manufacturer
    {
        return new Manufacturer(
            $model->id,
            $model->name,
            $model->name_kana,
            $model->display_name,
            $model->code,
            $model->url,
            $model->description,
            $model->country_code,
            $model->sort_order,
            $model->is_active,
        );

    }

}