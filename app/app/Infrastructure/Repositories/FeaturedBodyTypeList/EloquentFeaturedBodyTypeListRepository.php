<?php
namespace App\Infrastructure\Repositories\FeaturedBodyTypeList; 

use App\Domain\FeaturedBodyTypeList\Entities\FeaturedBodyType;
use App\Domain\FeaturedBodyTypeList\Repositories\FeaturedBodyTypeRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstFeaturedBodyTypes;

class EloquentFeaturedBodyTypeListRepository implements FeaturedBodyTypeRepositoryInterface
{
    private MstFeaturedBodyTypes $model;

    public function __construct(MstFeaturedBodyTypes $model)
    {
        $this->model = $model;
    }

    public function findActive(): array
    {
        $featuredBodyTypes = $this->model
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get();

        return $featuredBodyTypes->map(fn (MstFeaturedBodyTypes $featuredBodyType) => $this->toEntity($featuredBodyType))->all();
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