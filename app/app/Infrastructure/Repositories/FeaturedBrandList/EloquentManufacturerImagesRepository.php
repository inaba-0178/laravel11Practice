<?php
namespace App\Infrastructure\Repositories\FeaturedBrandList; 

use App\Domain\FeaturedBrandList\Entities\ManufacturerImage;
use App\Domain\FeaturedBrandList\Repositories\ManufacturerImageRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstManufacturerImages;

class EloquentManufacturerImagesRepository implements ManufacturerImageRepositoryInterface
{
    private MstManufacturerImages $model;

    public function __construct(MstManufacturerImages $model)
    {
        $this->model = $model;
    }

    //
    public function findByManufacturerIds(array $manufacturerIds, array $conditions = []): array
    {
        $manufacturerImages = $this->model
            ->whereIn('manufacturer_id', $manufacturerIds)
            ->orderBy('sort_order')
            ->get();

        return $manufacturerImages->map(fn (MstManufacturerImages $manufacturerImage) => $this->toEntity($manufacturerImage))->all();
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
            $model->file_path ?? '',
            $model->alt_text ?? '',
            $model->sort_order,
            $model->is_main,
            $model->is_active,
        );

    }

}