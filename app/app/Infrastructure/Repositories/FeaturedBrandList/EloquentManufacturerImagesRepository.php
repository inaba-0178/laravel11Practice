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

    public function findAll(): array
    {
        $manufacturerImages = $this->model
            ->get();

        return $this->toEntities($manufacturerImages);
    }

    public function findActive(): array
    {
        $manufacturerImages = $this->model
            ->get();

        return $this->toEntities($manufacturerImages);
    }

    public function findById(int $id): ?ManufacturerImage
    {
        $manufacturerImage = $this->model
            ->find($id);

        if (!$manufacturerImage) {
            return null;
        }

        return $this->toEntity($manufacturerImage);
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param MstManufacturerImages
     * @return ManufacturerImage
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

    /**
     * Eloquentコレクションをエンティティ配列に変換
     * 
     * @param  $models
     * @return array
     */
    private function toEntities($models): array
    {
        return $models->map(function ($model) {
            return $this->toEntity($model);
        })->all();
    }

    //
    public function findByManufacturerIds(array $manufacturerIds, array $conditions = []): array
    {
        return MstManufacturerImages::whereIn('manufacturer_id', $manufacturerIds)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($m) => new ManufacturerImage(
                $m->id,
                $m->manufacturer_id,
                $m->image_type,
                $m->file_path,
                $m->alt_text,
                $m->sort_order,
                $m->is_main,
                $m->is_active,
            ))
            ->toArray();
    }
}