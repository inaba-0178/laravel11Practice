<?php
namespace App\Infrastructure\Repositories\ManufacturerList; 

use App\Domain\ManufacturerList\Entities\Manufacturer;
use App\Domain\ManufacturerList\Repositories\ManufacturerRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstManufacturers;
use App\Infrastructure\Repositories\BaseRepository;
use App\Domain\Common\Constants\CacheConstants;
use Illuminate\Support\Facades\Cache;

class EloquentManufacturersRepository extends BaseRepository implements ManufacturerRepositoryInterface
{
    public function __construct(
        MstManufacturers $model
    ) {
        parent::__construct($model);
    }

    public function findAll(): array
    {
        return Cache::remember(CacheConstants::KEY_MANUFACTURERS, CacheConstants::TTL_MST, function () {
            return $this->toEntities($this->model->get(), fn($model) => $this->toEntity($model));
        });
    }

    public function findById(int $id): ?Manufacturer
    {
        return Cache::remember(CacheConstants::KEY_MANUFACTURERS . ':id:' . $id, CacheConstants::TTL_MST, function () use ($id) {
            $manufacturer = $this->model->find($id);

            if (!$manufacturer) {
                return null;
            }

            return $this->toEntity($manufacturer);
        });
    }

    /**
     * @param int[] $ids
     * @return Manufacturer[]
     */
    public function findByIds(array $ids): array
    {
        $cacheKey = CacheConstants::KEY_MANUFACTURERS . ':ids:' . md5(serialize($ids));
        return Cache::remember($cacheKey, CacheConstants::TTL_MST, function () use ($ids) {
            return $this->toEntities(
                $this->model->whereIn('id', $ids)->orderBy('sort_order')->get(),
                fn($model) => $this->toEntity($model)
            );
        });
    }

    /**
     * EloquentモデルをEntityに変換
     */
    private function toEntity(MstManufacturers $model): Manufacturer
    {
        return new Manufacturer(
            id: $model->id,
            name: $model->name,
            nameKana: $model->name_kana,
            displayName: $model->display_name,
            code: $model->code,
            url: $model->url,
            description: $model->description,
            countryCode: $model->country_code,
            sortOrder: $model->sort_order,
            isActive: $model->is_active,
        );
    }
}