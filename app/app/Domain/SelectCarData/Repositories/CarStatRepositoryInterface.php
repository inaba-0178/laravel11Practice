<?php
namespace App\Domain\SelectCarData\Repositories;

use App\Domain\SelectCarData\Exceptions\CarNotFoundException;
use App\Domain\SelectCarData\Entities\Car;

interface CarStatRepositoryInterface
{
    /**
     * 車両閲覧人気情報を取得
     * 
     * @param int $carId
     * @return Car
     * @throws CarNotFoundException
     */
    public function findById(int $carId): Car;
}