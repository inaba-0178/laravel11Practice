<?php
namespace App\Infrastructure\Repositories\SelectManufacturerList; 

use App\Domain\SelectManufacturerList\Entities\CarSerie;
use App\Domain\SelectManufacturerList\Repositories\CarSerieRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstCarSeries;
use App\Infrastructure\Repositories\BaseRepository;
use App\Constants\MstTableMap;

class EloquentCarSeriesRepository extends BaseRepository implements CarSerieRepositoryInterface
{
    public function __construct(MstCarSeries $model)
    {
        parent::__construct($model);
    }

    public function findByManufacturerId(int $manufacturerId, array $conditions = []): array
    {
        $carSeries = $this->model
            ->where('manufacturer_id', $manufacturerId)
            ->with(['mainImage'])  // ← メイン画像をEager Load
            ->get();

        return $this->toEntities($carSeries, fn($model) => $this->toEntity($model));
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

    /**
     * DBに保存されたファイル名（例: LEXUS/ct.jpg）から
     * MinIO/S3のフルURLを生成する
     *
     * 例: LEXUS/ct.jpg → http://localhost:9000/car-images/mst/car_series/LEXUS/ct.jpg
     */
    private function buildS3Url(string $tableName, string $filePath): string
    {
        if (empty($filePath)) return '';

        $folder  = MstTableMap::getImageFolder($tableName);
        $baseUrl = rtrim((string) config('filesystems.disks.s3.url'), '/');

        return "{$baseUrl}/{$folder}{$filePath}";
    }
}