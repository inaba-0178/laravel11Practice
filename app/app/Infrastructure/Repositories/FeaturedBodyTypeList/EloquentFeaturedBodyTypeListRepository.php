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

    public function findAll(): array
    {
        $featuredBodyTypeLists = $this->model
            ->get();

        return $this->toEntities($featuredBodyTypeLists);
    }

    public function findActive(): array
    {
        $featuredBodyTypes = $this->model
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get();
        return $this->toEntities($featuredBodyTypes);
    }

    public function findById(int $id): ?FeaturedBodyType
    {
        $featuredBodyType = $this->model
            ->find($id);

        if (!$featuredBodyType) {
            return null;
        }

        return $this->toEntity($featuredBodyType);
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param MstFeaturedBodyTypes
     * @return FeaturedBodyType
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

}