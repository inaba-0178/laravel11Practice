<?php
namespace App\Domain\RegionList\Repositories;

interface RegionRepositoryInterface
{
    /**
     * すべてのRegionを取得
     * 
     * @return Region[]
     */
    public function findAll(): array;
}