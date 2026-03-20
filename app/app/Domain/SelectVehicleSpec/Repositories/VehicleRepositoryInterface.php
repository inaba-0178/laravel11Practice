<?php

namespace App\Domain\SelectVehicleSpec\Repositories;

use App\Domain\SelectVehicleSpec\Entities\Vehicle;

interface VehicleRepositoryInterface
{
    /**
     * 車両情報を取得
     *
     * @param int $vehicleId
     * @return Vehicle
     */
    public function findById(int $vehicleId): Vehicle;
}