<?php
namespace App\Domain\RidingCapacityList\Repositories;

interface RidingCapacityRepositoryInterface
{
    /**
     * すべてのRidingCapacityを取得
     * 
     * @return RidingCapacity[]
     */
    public function findAll(): array;
}