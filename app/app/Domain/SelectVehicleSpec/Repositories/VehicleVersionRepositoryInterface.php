<?php

namespace App\Domain\SelectVehicleSpec\Repositories;

use App\Domain\SelectVehicleSpec\Entities\VehicleVersion;

interface VehicleVersionRepositoryInterface
{
    /**
     * 車両年式バージョン情報を取得
     *
     * @param int $vehicleId
     * @return VehicleVersion
     */
    public function findByVehicleId(int $vehicleId): VehicleVersion;
}