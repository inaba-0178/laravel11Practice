<?php
namespace App\Domain\FeaturedBodyTypeList\Repositories;

interface FeaturedBodyTypeRepositoryInterface
{
    /**
     * すべてのFeaturedBodyTypeを取得
     * 
     * @return FeaturedBodyType[]
     */
    public function findAll(): array;

    /**
     * アクティブなFeaturedBodyTypeを取得
     * 
     * @return FeaturedBodyType[]
     */
    public function findActive(): array;
}