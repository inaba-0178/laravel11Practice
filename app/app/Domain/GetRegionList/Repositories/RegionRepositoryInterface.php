<?php
namespace App\Domain\GetRegionList\Repositories;

use App\Domain\GetRegionList\Entities;

interface RegionRepositoryInterface
{
    /**
     * すべてのRegionを取得
     * 
     * @return Region[]
     */
    public function findAll(): array;

    /**
     * アクティブなRegionのみを取得
     * 
     * @return Region[]
     */
    public function findActive(): array;

    /**
     * IDでRegionを取得
     * 
     * @param int $id
     * @return Region|null
     */
    //public function findById(int $id): ?Region;
}