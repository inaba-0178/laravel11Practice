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

    public function findByBodyTypeIds(array $bodyTypeIds, array $conditions = []): array
    {
        $bodyTypeImages = $this->model
            ->whereIn('body_type_id', $bodyTypeIds)
            ->orderBy('sort_order')
            ->get();

        return $bodyTypeImages->map(fn (MstBodyTypeImages $bodyTypeImage) => $this->toEntity($bodyTypeImage))->all();
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param MstBodyTypeImages
     * @return BodyTypeImage
     */
    private function toEntity(MstBodyTypeImages $model): BodyTypeImage
    {
        return new BodyTypeImage(
            $model->id,
            $model->body_type_id,
            $model->image_type ?? '',
            $model->file_path ?? '',
            $model->alt_text ?? '',
            $model->sort_order,
            $model->is_main,
            $model->is_active,
        );

    }
}