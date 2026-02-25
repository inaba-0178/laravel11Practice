<?php
namespace App\Domain\FeaturedBrandList\Repositories;

interface ManufacturerRepositoryInterface
{
    /**
     * 選択されたcodeのデータ全取得
     * 
     * @return Manufacturer[]
     */
    public function findByCodes(array $codes, array $conditions = []): array;
}