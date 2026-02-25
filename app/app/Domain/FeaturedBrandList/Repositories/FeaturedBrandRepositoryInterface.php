<?php
namespace App\Domain\FeaturedBrandList\Repositories;

interface FeaturedBrandRepositoryInterface
{
    /**
     * アクティブなFeaturedBrandを取得
     * 
     * @return FeaturedBrand[]
     */
    public function findActive(): array;
}