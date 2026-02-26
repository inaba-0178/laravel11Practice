<?php
namespace App\Domain\SelectCarData\Repositories;

use App\Domain\SelectCarData\Exceptions\CarNotFoundException;

interface CarImageRepositoryInterface
{
    /**
     * 車両イメージ情報を取得
     * 
     * @param   int $carId
     * @return  array
     * @throws  CarNotFoundException
     */
    public function findByCarId(int $carId): array;
}