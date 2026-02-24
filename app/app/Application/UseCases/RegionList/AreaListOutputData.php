<?php

namespace App\Application\UseCases\RegionList;

use App\Domain\RegionList\Entities\Area;

class AreaListOutputData
{
    private array $areas;
    private int $count;

    /**
     * @param Area[] $areas
     */
    public function __construct(array $areas)
    {
        $this->areas = $areas;
        $this->count = count($areas);
    }

    public function toArray(): array
    {
        return [
            'data' => [
                'areas' => array_map(fn(Area $area) => $area->toArray(), $this->areas),
                'count' => $this->count,
            ],
        ];
    }
}