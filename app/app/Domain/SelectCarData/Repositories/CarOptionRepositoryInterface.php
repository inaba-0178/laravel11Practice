<?php
namespace App\Domain\SelectCarData\Repositories;

use App\Domain\SelectCarData\Exceptions\CarNotFoundException;

interface CarOptionRepositoryInterface
{
    /**
     * 車両オプション情報を取得
     * 
     * @param   int $carId
     * @return  array
     * @throws  CarNotFoundException
     */
    public function findByCarId(int $carId): array;
}