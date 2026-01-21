<?php
namespace App\Domain\GetRegionList\Repositories;

interface RegionRepositoryInterface
{
    /**
     * すべてのRegionを取得
     * 
     * @return Region[]
     */
    public function findAll(): array;
}