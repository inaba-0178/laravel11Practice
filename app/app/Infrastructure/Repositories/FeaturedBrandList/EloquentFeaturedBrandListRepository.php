<?php
namespace App\Infrastructure\Repositories\FeaturedBrandList; 

use App\Domain\FeaturedBrandList\Entities\FeaturedBrand;
use App\Domain\FeaturedBrandList\Repositories\FeaturedBrandRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstFeaturedBrands;

class EloquentFeaturedBrandListRepository implements FeaturedBrandRepositoryInterface
{
    private MstFeaturedBrands $model;

    public function __construct(MstFeaturedBrands $model)
    {
        $this->model = $model;
    }

    public function findAll(): array
    {
        $featuredBrandLists = $this->model
            ->get();

        return $this->toEntities($featuredBrandLists);
    }

    public function findActive(): array
    {
        $featuredBrands = $this->model
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get();
        return $this->toEntities($featuredBrands);
    }

    public function findById(int $id): ?FeaturedBrand
    {
        $featuredBrand = $this->model
            ->find($id);

        if (!$featuredBrand) {
            return null;
        }

        return $this->toEntity($featuredBrand);
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param MstFeaturedBrands
     * @return FeaturedBrand
     */
    private function toEntity(MstFeaturedBrands $model): FeaturedBrand
    {
        return new FeaturedBrand(
            $model->id,
            $model->manufacturer_code,
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