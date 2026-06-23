<?php
namespace App\Infrastructure\Repositories\SelectManufacturerList; 

use App\Domain\SelectManufacturerList\Repositories\ManufacturerRepositoryInterface;
use App\Domain\SelectManufacturerList\Entities\Manufacturer;
use App\Infrastructure\Eloquent\Mst\MstManufacturers;
use App\Domain\Common\Constants\CacheConstants;
use Illuminate\Support\Facades\Cache;

class EloquentManufacturersRepository implements ManufacturerRepositoryInterface
{
    private MstManufacturers $model;

    public function __construct(MstManufacturers $model)
    {
        $this->model = $model;
    }

    public function findByName(string $name): ?Manufacturer
    {
        $cacheKey = CacheConstants::KEY_MANUFACTURERS . ':name:' . md5($name);
        return Cache::remember($cacheKey, CacheConstants::TTL_MST, function () use ($name) {
            $manufacturer = $this->model::where('name', $name)->first();
            return $manufacturer ? $this->toEntity($manufacturer) : null;
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
            $model->display_name,
            $model->url,
            $model->description,
            $model->country_code,
            $model->is_active,
        );
    }
}