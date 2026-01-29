<?php
namespace App\Infrastructure\Repositories\FeaturedBodyTypeList; 

use App\Domain\FeaturedBodyTypeList\Entities\BodyTypeImage;
use App\Domain\FeaturedBodyTypeList\Repositories\BodyTypeImageRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstBodyTypeImages;

class EloquentBodyTypeImagesRepository implements BodyTypeImageRepositoryInterface
{
    private MstBodyTypeImages $model;

    public function __construct(MstBodyTypeImages $model)
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
    public function findByBodyTypeIds(array $bodyTypeIds, array $conditions = []): array
    {
        return MstBodyTypeImages::whereIn('body_type_id', $bodyTypeIds)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($m) => new BodyTypeImage(
                $m->id,
                $m->body_type_id,
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