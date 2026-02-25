<?php
namespace App\Domain\FeaturedBrandList\Repositories;

interface ManufacturerImageRepositoryInterface
{

    /**
     * 指定されたメーカーIDに紐づく画像を取得
     * 
     * @param int[] $manufacturerIds
     * @param array $conditions
     * @return ManufacturerImage[]
     */
    public function findByManufacturerIds(array $manufacturerIds, array $conditions = []): array;

}