<?php
namespace App\Infrastructure\Repositories\FeaturedBodyTypeList; 

use App\Domain\FeaturedBodyTypeList\Entities\BodyType;
use App\Domain\FeaturedBodyTypeList\Repositories\BodyTypeRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstBodyTypes;
use App\Infrastructure\Repositories\BaseRepository;
use App\Domain\Common\Constants\CacheConstants;
use Illuminate\Support\Facades\Cache;

class EloquentBodyTypesRepository extends BaseRepository implements BodyTypeRepositoryInterface
{

    public function __construct(MstBodyTypes $model)
    {
        parent::__construct($model);
    }

    public function findByCodes(array $codes, array $conditions = []): array
    {
        $cacheKey = CacheConstants::KEY_BODY_TYPES . ':codes:' . md5(serialize($codes));
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
     * @param   MstBodyTypes
     * @return  BodyType
     */
    private function toEntity(MstBodyTypes $model): BodyType
    {
        return new BodyType(
            $model->id,
            $model->name,
            $model->name_kana,
            $model->code,
            $model->description,
            $model->available_countries,
            $model->sort_order,
            $model->is_active,
        );

    }
}