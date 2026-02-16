<?php

namespace App\Application\UseCases\ManufacturerList;

use App\Domain\ManufacturerList\Entities\Manufacturer;

class ManufacturerListOutputData
{
    private ?array $manufacturerIds;
    private int $count;

    /**
     * @param manufacturerIds[] $manufacturerIds
     */
    public function __construct(
        array $manufacturerIds,
    )
    {
        $this->manufacturerIds = $manufacturerIds;
        $this->count = count($manufacturerIds);
    }

    public function toArray(): array
    {
        return [
            'success' => true,
            'data' => [
                'ManufacturerList' => array_map(
                    fn(Manufacturer $manufacturer) => $manufacturer->toArray(), 
                    $this->manufacturerIds
                ),
                'count' => $this->count,
            ],
        ];
    }

    public function getManufacturerInfo(): array
    {
        return $this->manufacturerInfo;
    }

    public function getCount(): int
    {
        return $this->count;
    }

}