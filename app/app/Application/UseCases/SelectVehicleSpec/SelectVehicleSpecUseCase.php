<?php

namespace App\Application\UseCases\SelectVehicleSpec;

use App\Domain\SelectVehicleSpec\Repositories\VehicleRepositoryInterface;
use App\Domain\SelectVehicleSpec\Repositories\VehicleVersionRepositoryInterface;
use App\Domain\SelectVehicleSpec\ValueObjects\VehicleId;

class SelectVehicleSpecUseCase
{
    public function __construct(
        private readonly VehicleRepositoryInterface        $vehicleRepository,
        private readonly VehicleVersionRepositoryInterface $vehicleVersionRepository,
    ) {}

    public function execute(VehicleId $vehicleId): SelectVehicleSpecOutputData
    {
        $vehicle        = $this->vehicleRepository->findById($vehicleId->getValue());
        $vehicleVersion = $this->vehicleVersionRepository->findByVehicleId($vehicle->getId());

        return new SelectVehicleSpecOutputData($vehicle, $vehicleVersion);
    }
}