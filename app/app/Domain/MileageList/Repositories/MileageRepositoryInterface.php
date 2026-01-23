<?php
namespace App\Domain\MileageList\Repositories;

interface MileageRepositoryInterface
{
    /**
     * すべてのMileageを取得
     * 
     * @return Mileage[]
     */
    public function findAll(): array;
}