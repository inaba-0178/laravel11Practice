<?php
namespace App\Domain\CarList\Repositories;

use App\Domain\CarList\Exceptions\CarNotFoundException;

interface CarDetailRepositoryInterface
{
    /**
     * 車両詳細情報一覧を取得
     * 
     * @param array $carId
     * @return array
     * @throws CarNotFoundException
     */
    public function findByCarId(array $carId): array;
}