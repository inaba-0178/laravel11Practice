<?php
namespace App\Domain\FeaturedBrandList\Repositories;

interface ManufacturerImageRepositoryInterface
{
    /**
     * すべてのManufacturerImageを取得
     * 
     * @return ManufacturerImage[]
     */
    public function findAll(): array;

    /**
     * 指定されたメーカーIDに紐づく画像を取得
     * 
     * @param int[] $manufacturerIds
     * @param array $conditions
     * @return ManufacturerImage[]
     */
    public function findByManufacturerIds(array $manufacturerIds, array $conditions = []): array;

}