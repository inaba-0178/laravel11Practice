<?php

namespace App\Application\UseCases\FeaturedBrandList;

use App\Domain\FeaturedBrandList\Entities\ManufacturerInfo;
class FeaturedBrandListOutputData
{
    private ?array $manufacturerInfo;
    private int $count;

    /**
     * @param ManufacturerInfo[] $manufacturerInfo
     */
    public function __construct(
        array $manufacturerInfo,
    )
    {
        $this->manufacturerInfo = $manufacturerInfo;
        $this->count = count($manufacturerInfo);
    }

    public function toArray(): array
    {
        return [
            'success' => true,
            'data' => [
                'ManufacturerInfo' => array_map( fn($item) => $item instanceof ManufacturerInfo ? $item->toArray() : $item, $this->manufacturerInfo),
                'count' => $this->count,
            ],
        ];
    }

    public function ManufacturerInfo(): array
    {
        return $this->manufacturerInfo;
    }

    public function getCount(): int
    {
        return $this->count;
    }

}