<?php

namespace App\Application\UseCases\SelectBodyTypeList;

use App\Domain\SelectBodyTypeList\Entities\CarSerie;
use App\Domain\SelectBodyTypeList\Entities\BodyType;

class SelectBodyTypeListOutputData
{
    //private ?BodyType $BodyType = null;
    private array $carSeries = [];
    //private ?array $groupedCarSeries = null;
    private int $count;

    /**
     * @param CarSerie[] $carSerie
     */
    public function __construct(
      //  ?BodyType $BodyType = null,
        array $carSeries,
       // ?array $groupedCarSeries = null
    )
    {
        //$this->BodyType = $BodyType;
        $this->carSeries = $carSeries;
        //$this->groupedCarSeries = $groupedCarSeries;
        $this->count = count($carSeries);
    }

    public function toArray(): array
    {
        $response = [
            'success' => true,
            'data' => [
                'BodyTypeCarList' => $this->carSeries,
                'count' => $this->count,
            ],
        ];

        // グルーピングデータがあれば追加
        // if ($this->groupedCarSeries !== null) {
        //     $response['data']['groupedByInitial'] = $this->formatGroupedData($this->groupedCarSeries);
        // }

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

    public function getBodyType(): array
    {
        return $this->BodyType;
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