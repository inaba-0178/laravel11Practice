<?php
namespace App\Domain\FeaturedBodyTypeList\Repositories;

interface FeaturedBodyTypeRepositoryInterface
{
    /**
     * アクティブなFeaturedBodyTypeを取得
     * 
     * @return FeaturedBodyType[]
     */
    public function findActive(): array;
}