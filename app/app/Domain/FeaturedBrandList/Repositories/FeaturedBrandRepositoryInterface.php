<?php
namespace App\Domain\FeaturedBrandList\Repositories;

interface FeaturedBrandRepositoryInterface
{
    /**
     * すべてのFeaturedBrandを取得
     * 
     * @return FeaturedBrand[]
     */
    public function findAll(): array;
}