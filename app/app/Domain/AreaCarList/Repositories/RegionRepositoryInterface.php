<?php
namespace App\Domain\AreaCarList\Repositories;

interface RegionRepositoryInterface
{
    /**
     * すべてのRegionを取得
     * 
     * @return Region[]
     */
    public function findAll(): array;
}