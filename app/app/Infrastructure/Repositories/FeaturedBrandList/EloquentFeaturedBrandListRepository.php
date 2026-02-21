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

    public function findActive(): array
    {
        $featuredBrands = $this->model
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get();

        return $featuredBrands->map(fn (MstFeaturedBrands $featuredBrand) => $this->toEntity($featuredBrand))->all();
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param   MstFeaturedBrands
     * @return  FeaturedBrand
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
}