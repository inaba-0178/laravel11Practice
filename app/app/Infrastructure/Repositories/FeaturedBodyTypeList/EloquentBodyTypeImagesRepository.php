<?php
namespace App\Infrastructure\Repositories\FeaturedBodyTypeList; 

use App\Domain\FeaturedBodyTypeList\Entities\BodyTypeImage;
use App\Domain\FeaturedBodyTypeList\Repositories\BodyTypeImageRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstBodyTypeImages;
use App\Infrastructure\Repositories\BaseRepository;
use App\Constants\MstTableMap;

class EloquentBodyTypeImagesRepository extends BaseRepository implements BodyTypeImageRepositoryInterface
{
    public function __construct(MstBodyTypeImages $model)
    {
        parent::__construct($model);
    }

    public function findByBodyTypeIds(array $bodyTypeIds, array $conditions = []): array
    {
        $bodyTypeImages = $this->model
            ->whereIn('body_type_id', $bodyTypeIds)
            ->orderBy('sort_order')
            ->get();

        return $this->toEntities($bodyTypeImages, fn($model) => $this->toEntity($model));
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param   MstBodyTypeImages
     * @return  BodyTypeImage
     */
    private function toEntity(MstBodyTypeImages $model): BodyTypeImage
    {
        return new BodyTypeImage(
            $model->id,
            $model->body_type_id,
            $model->image_type ?? '',
            $this->buildS3Url('mst_body_type_images', $model->file_path ?? ''),
            $model->alt_text ?? '',
            $model->sort_order,
            $model->is_main,
            $model->is_active,
        );
    }
}