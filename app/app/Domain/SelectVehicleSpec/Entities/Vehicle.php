<?php

namespace App\Domain\SelectVehicleSpec\Entities;

final class Vehicle
{
    public function __construct(
        public readonly int     $id,
        public readonly int     $seriesId,
        public readonly int     $manufacturerId,
        public readonly string  $name,
        public readonly ?string $modelCode,
        public readonly ?string $bodyType,
        public readonly ?string $countryCode,
        public readonly string  $status,
    ) {}

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }

    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'seriesId'     => $this->seriesId,
            'manufacturerId' => $this->manufacturerId,
            'name'         => $this->name,
            'modelCode'    => $this->modelCode ?? '',
            'bodyType'     => $this->bodyType ?? '',
            'countryCode'  => $this->countryCode ?? '',
            'status'       => $this->status,
        ];
    }
}