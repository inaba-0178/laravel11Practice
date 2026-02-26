<?php
namespace App\Domain\SelectCarData\Repositories;

use App\Domain\SelectCarData\Exceptions\CarNotFoundException;
use App\Domain\SelectCarData\Entities\CarDetail;

interface CarDetailRepositoryInterface
{
    /**
     * 車両詳細情報一覧を取得
     * 
     * @param int $carId
     * @return CarDetail
     * @throws CarNotFoundException
     */
    public function findByCarId(int $carId): CarDetail;
}