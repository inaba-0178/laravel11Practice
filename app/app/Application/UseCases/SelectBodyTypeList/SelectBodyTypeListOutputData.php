<?php

namespace App\Application\UseCases\SelectBodyTypeList;

use App\Domain\SelectBodyTypeList\Entities\CarSerie;

class SelectBodyTypeListOutputData
{
    private array   $carSeries = [];
    private int     $count;

    /**
     * @param CarSerie[] $carSerie
     */
    public function __construct(
        array $carSeries,
    )
    {
        $this->carSeries    = $carSeries;
        $this->count        = count($carSeries);
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

        return $response;
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