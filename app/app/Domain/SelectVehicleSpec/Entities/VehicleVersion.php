<?php

namespace App\Domain\SelectVehicleSpec\Entities;

final class VehicleVersion
{
    public function __construct(
        public readonly int     $id,
        public readonly int     $vehicleId,
        public readonly int     $yearModel,
        public readonly ?int    $displacementCc,
        public readonly ?string $driveType,
        public readonly ?float  $fuelEfficiencyFrom,
        public readonly ?float  $fuelEfficiencyTo,
        public readonly ?int    $maxPowerKw,
        public readonly ?string $transmissionType,
        public readonly ?int    $weightKg,
        public readonly bool    $isLatest,
    ) {}

    public function getId(): int { return $this->id; }
    public function getVehicleId(): int { return $this->vehicleId; }

    public function toArray(): array
    {
        return [
            'id'                 => $this->id,
            'vehicleId'          => $this->vehicleId,
            'yearModel'          => $this->yearModel,
            'displacementCc'     => $this->displacementCc,
            'driveType'          => $this->driveType ?? '',
            'fuelEfficiencyFrom' => $this->fuelEfficiencyFrom,
            'fuelEfficiencyTo'   => $this->fuelEfficiencyTo,
            'maxPowerKw'         => $this->maxPowerKw,
            'transmissionType'   => $this->transmissionType ?? '',
            'weightKg'           => $this->weightKg,
            'isLatest'           => $this->isLatest,
        ];
    }
}