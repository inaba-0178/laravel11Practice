<?php

namespace App\Application\UseCases\SelectVehicleSpec;

use App\Domain\SelectVehicleSpec\Entities\Vehicle;
use App\Domain\SelectVehicleSpec\Entities\VehicleVersion;

class SelectVehicleSpecOutputData
{
    public function __construct(
        private readonly Vehicle        $vehicle,
        private readonly VehicleVersion $vehicleVersion,
    ) {}

    public function toArray(): array
    {
        return [
            'success'        => true,
            'vehicle'        => (fn(Vehicle $vehicle) => $vehicle->toArray())($this->vehicle),
            'vehicleVersion' => (fn(VehicleVersion $version) => $version->toArray())($this->vehicleVersion),
        ];
    }
}