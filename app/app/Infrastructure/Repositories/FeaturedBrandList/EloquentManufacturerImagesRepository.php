<?php
namespace App\Infrastructure\Repositories\FeaturedBrandList; 

use App\Domain\FeaturedBrandList\Entities\ManufacturerImage;
use App\Domain\FeaturedBrandList\Repositories\ManufacturerImageRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstManufacturerImages;
use App\Infrastructure\Repositories\BaseRepository;
use App\Constants\MstTableMap;

class EloquentManufacturerImagesRepository extends BaseRepository implements ManufacturerImageRepositoryInterface
{

    public function __construct(MstManufacturerImages $model)
    {
        parent::__construct($model);
    }

    public function findByManufacturerIds(array $manufacturerIds, array $conditions = []): array
    {
        $manufacturerImages = $this->model
            ->whereIn('manufacturer_id', $manufacturerIds)
            ->orderBy('sort_order')
            ->get();

        return $this->toEntities($manufacturerImages, fn($model) => $this->toEntity($model));
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param   MstManufacturerImages
     * @return  ManufacturerImage
     */
    private function toEntity(MstManufacturerImages $model): ManufacturerImage
    {
        return new ManufacturerImage(
            $model->id,
            $model->manufacturer_id,
            $model->image_type ?? '',
            $this->buildS3Url('mst_manufacturer_images', $model->file_path ?? ''),
            $model->alt_text ?? '',
            $model->sort_order,
            $model->is_main,
            $model->is_active,
        );
    }

    /**
     * DBに保存されたファイル名（例: toyota.jpg）から
     * MinIO/S3のフルURLを生成する
     *
     * DBには管理しやすいようファイル名のみ保存しており、
     * テーブル名からS3フォルダパスを解決してURLを組み立てる
     *
     * 例: toyota.jpg → http://minio:9000/car-images/mst/manufacturers/toyota.jpg
     */
    private function buildS3Url(string $tableName, string $filePath): string
    {
        if (empty($filePath)) return '';

        $folder  = MstTableMap::getImageFolder($tableName);
        $baseUrl = rtrim(config('filesystems.disks.s3.url'), '/');

        return "{$baseUrl}/{$folder}{$filePath}";
    }
}