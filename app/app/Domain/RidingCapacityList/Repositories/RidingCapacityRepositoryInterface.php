<?php
namespace App\Domain\RidingCapacityList\Repositories;

interface RidingCapacityRepositoryInterface
{
    /**
     * RidingCapacityを取得
     * 
     * @return RidingCapacity[]
     */
    public function findActive(): array;
}