<?php

namespace App\Application\UseCases\SelectManufacturerList;

use App\Domain\SelectManufacturerList\Entities\CarSerie;
use App\Domain\SelectManufacturerList\Entities\Manufacturer;

class SelectManufacturerListOutputData
{
    private ?Manufacturer $manufacturer = null;
    private array $carSeries = [];
    private ?array $groupedCarSeries = null;
    private int $count;

    /**
     * @param CarSerie[] $carSerie
     */
    public function __construct(
        ?Manufacturer $manufacturer = null,
        array $carSeries,
        ?array $groupedCarSeries = null
    )
    {
        $this->manufacturer = $manufacturer;
        $this->carSeries = $carSeries;
        $this->groupedCarSeries = $groupedCarSeries;
        $this->count = count($carSeries);
    }

    public function toArray(): array
    {
        $response = [
            'success' => true,
            'data' => [
                'ManufacturerInfo' => $this->manufacturer?->toArray(),
                'VehicleInfo' => array_map(fn(CarSerie $carSerie) => $carSerie->toArray(), $this->carSeries),
                'count' => $this->count,
            ],
        ];

        // グルーピングデータがあれば追加
        if ($this->groupedCarSeries !== null) {
            $response['data']['groupedByInitial'] = $this->formatGroupedData($this->groupedCarSeries);
        }

        return $response;
    }

    private function formatGroupedData(array $groupedCarSeries): array
    {
        $formatted = [];
        
        foreach ($groupedCarSeries as $key => $carSeries) {
            $formatted[$key] = array_map(
                fn(CarSerie $carSerie) => $carSerie->toArray(), 
                $carSeries
            );
        }
        
        return $formatted;
    }

    public function getManufacturer(): array
    {
        return $this->manufacturer;
    }

    public function getCarSeries(): array
    {
        return $this->carSeries;
    }

    public function getGroupedCarSeries(): ?array
    {
        return $this->groupedCarSeries;
    }

    public function getCount(): int
    {
        return $this->count;
    }

}