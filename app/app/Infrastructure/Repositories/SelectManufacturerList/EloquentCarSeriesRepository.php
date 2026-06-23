<?php
namespace App\Infrastructure\Repositories\SelectManufacturerList; 

use App\Domain\SelectManufacturerList\Entities\CarSerie;
use App\Domain\SelectManufacturerList\Repositories\CarSerieRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstCarSeries;
use App\Infrastructure\Repositories\BaseRepository;
use App\Domain\Common\Constants\CacheConstants;
use Illuminate\Support\Facades\Cache;

class EloquentCarSeriesRepository extends BaseRepository implements CarSerieRepositoryInterface
{
    public function __construct(MstCarSeries $model)
    {
        parent::__construct($model);
    }

    public function findByManufacturerId(int $manufacturerId, array $conditions = []): array
    {
        $cacheKey = CacheConstants::KEY_CAR_SERIES . ':manufacturer:' . $manufacturerId;
        return Cache::remember($cacheKey, CacheConstants::TTL_MST, function () use ($manufacturerId) {
            return $this->toEntities(
                $this->model->where('manufacturer_id', $manufacturerId)->with(['mainImage'])->get(),
                fn($model) => $this->toEntity($model)
            );
        });
    }

    private function toEntity(MstCarSeries $model): CarSerie
    {
        $imageFilePath = '';
        if ($model->mainImage) {
            $imageFilePath = $this->buildS3Url('mst_car_series_images', $model->mainImage->file_path ?? '');
        }

        return new CarSerie(
            $model->series_id,
            $model->series_name,
            $model->manufacturer_id,
            $imageFilePath,
        );
    }

}