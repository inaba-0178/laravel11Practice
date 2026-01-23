<?php

namespace App\Application\UseCases\RidingCapacityList;

use App\Domain\RidingCapacityList\Entities\RidingCapacity;

class RidingCapacityListOutputData
{
    private ?array $ridingCapacities;
    private int $count;

    /**
     * @param RidingCapacity[] $ridingCapacities
     */
    public function __construct(array $ridingCapacities)
    {
        $this->ridingCapacities = $ridingCapacities;
        $this->count = count($ridingCapacities);
    }

    public function toArray(): array
    {
        return [
            'success' => true,
            'data' => [
                'RidingCapacityList' => array_map(fn(RidingCapacity $ridingCapacity) => $ridingCapacity->toArray(), $this->ridingCapacities),
                'count' => $this->count,
            ],
        ];
    }

    public function RidingCapacity(): array
    {
        return $this->ridingCapacities;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}