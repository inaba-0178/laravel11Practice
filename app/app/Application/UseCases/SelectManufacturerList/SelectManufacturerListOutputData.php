<?php

namespace App\Application\UseCases\SelectManufacturerList;

use App\Domain\SelectManufacturerList\Entities\CarSerie;

class SelectManufacturerListOutputData
{
    private array $carSeries = [];
    private int $count;

    /**
     * @param CarSerie[] $carSerie
     */
    public function __construct(
        array $carSeries,
    )
    {
        $this->carSeries = $carSeries;
        $this->count = count($carSeries);
    }

    public function toArray(): array
    {
        return [
            'success' => true,
            'data' => [
                'VehicleInfo' => array_map(fn(CarSerie $carSerie) => $carSerie->toArray(), $this->carSeries),
                'count' => $this->count,
            ],
        ];
    }

    public function getCarSeries(): array
    {
        return $this->carSeries;
    }

    public function getCount(): int
    {
        return $this->count;
    }

}