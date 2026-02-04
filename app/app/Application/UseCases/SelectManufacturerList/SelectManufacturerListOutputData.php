<?php

namespace App\Application\UseCases\SelectManufacturerList;

use App\Domain\SelectManufacturerList\Entities\CarSerie;

class SelectManufacturerListOutputData
{
    private ?array $carSerie = [];
    private int $count;

    /**
     * @param CarSerie[] $carSerie
     */
    public function __construct(
        array $carSerie,
    )
    {
        $this->carSerie = $carSerie;
        $this->count = count($carSerie);
    }

    public function toArray(): array
    {
        return [
            'success' => true,
            'data' => [
                'VehicleInfo' => array_map(fn(CarSerie $carSerie) => $carSerie->toArray(), $this->carSerie),
                'count' => $this->count,
            ],
        ];
    }

    public function getCarSerie(): array
    {
        return $this->carSerie;
    }

    public function getCount(): int
    {
        return $this->count;
    }

}