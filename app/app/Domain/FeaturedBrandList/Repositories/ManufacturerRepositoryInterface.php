<?php
namespace App\Domain\FeaturedBrandList\Repositories;

interface ManufacturerRepositoryInterface
{
    /**
     * すべてのmanufacturerを取得
     * 
     * @return Manufacturer[]
     */
    public function findAll(): array;

    public function findByCodes(array $codes, array $conditions = []): array;
}