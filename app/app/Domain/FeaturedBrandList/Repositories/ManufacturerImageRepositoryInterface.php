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
}