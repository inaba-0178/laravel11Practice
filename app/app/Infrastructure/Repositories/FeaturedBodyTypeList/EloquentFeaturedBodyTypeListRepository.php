<?php
namespace App\Infrastructure\Repositories\FeaturedBodyTypeList; 

use App\Domain\FeaturedBodyTypeList\Entities\FeaturedBodyType;
use App\Domain\FeaturedBodyTypeList\Repositories\FeaturedBodyTypeRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstFeaturedBodyTypes;
use App\Infrastructure\Repositories\BaseRepository;

class EloquentFeaturedBodyTypeListRepository extends BaseRepository implements FeaturedBodyTypeRepositoryInterface
{

    public function __construct(MstFeaturedBodyTypes $model)
    {
        parent::__construct($model);
    }

    public function findActive(): array
    {
        $featuredBodyTypes = $this->model
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get();

        return $this->toEntities($featuredBodyTypes, fn($model) => $this->toEntity($model));
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param   MstFeaturedBodyTypes
     * @return  FeaturedBodyType
     */
    private function toEntity(MstFeaturedBodyTypes $model): FeaturedBodyType
    {
        return new FeaturedBodyType(
            $model->id,
            $model->body_type_code,
            $model->position,
            $model->sort_order,
            $model->is_active,
        );

    }
}