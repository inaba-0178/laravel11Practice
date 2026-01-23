<?php

namespace App\Application\UseCases\MileageList;

use App\Domain\MileageList\Entities\Mileage;

class MileageListOutputData
{
    private ?array $mileages;
    private int $count;

    /**
     * @param Mileage[] $mileages
     */
    public function __construct(array $mileages)
    {
        $this->mileages = $mileages;
        $this->count = count($mileages);
    }

    public function toArray(): array
    {
        return [
            'success' => true,
            'data' => [
                'MileageList' => array_map(fn(Mileage $mileage) => $mileage->toArray(), $this->mileages),
                'count' => $this->count,
            ],
        ];
    }

    public function Mileages(): array
    {
        return $this->mileages;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}