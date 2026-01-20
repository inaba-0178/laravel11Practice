<?php

namespace App\Application\UseCases\GetRegionList;

use App\Domain\GetRegionList\Entities\Region;

class GetRegionListOutputData
{
    private ?array $regions;
    private int $count;

    /**
     * @param Region[] $regions
     */
    public function __construct(array $regions)
    {
        $this->regions = $regions;
        $this->count = count($regions);
    }

    public function toArray(): array
    {
        return [
            'data' => [
                'regions' => array_map(fn(Region $region) => $region->toArray(), $this->regions),
                'count' => $this->count,
            ],
        ];
    }

    public function getRegions(): array
    {
        return $this->regions;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}