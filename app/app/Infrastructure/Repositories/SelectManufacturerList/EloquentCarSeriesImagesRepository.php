<?php
namespace App\Infrastructure\Repositories\SelectManufacturerList;

use App\Domain\SelectManufacturerList\Entities\CarSeriesImage;
use App\Domain\SelectManufacturerList\Repositories\CarSeriesImageRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstCarSeriesImages;
use App\Infrastructure\Repositories\BaseRepository;
use App\Constants\MstTableMap;

class EloquentCarSeriesImagesRepository extends BaseRepository implements CarSeriesImageRepositoryInterface
{
    public function __construct(MstCarSeriesImages $model)
    {
        parent::__construct($model);
    }

    public function findBySeriesIds(array $seriesIds, array $conditions = []): array
    {
        $images = $this->model
            ->whereIn('series_id', $seriesIds)
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get();

        return $this->toEntities($images, fn($model) => $this->toEntity($model));
    }

    /**
     * EloquentモデルをEntityに変換
     *
     * @param MstCarSeriesImages
     * @return CarSeriesImage
     */
    private function toEntity(MstCarSeriesImages $model): CarSeriesImage
    {
        return new CarSeriesImage(
            $model->id,
            $model->series_id,
            $this->buildS3Url('mst_car_series_images', $model->file_path ?? ''),
            $model->alt_text ?? '',
            $model->sort_order,
            $model->is_main,
            $model->is_active,
        );
    }

}