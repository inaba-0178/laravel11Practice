<?php
namespace App\Application\UseCases\ManufacturerList;

use App\Domain\ManufacturerList\Entities\Manufacturer;

class ManufacturerListOutputData
{
    /**
     * @param Manufacturer[] $manufacturers
     */
    public function __construct(
        private readonly array $manufacturers,
    ) {}

    public function toArray(): array
    {
        return [
            'success' => true,
            'data' => [
                'ManufacturerList' => array_map(
                    fn(Manufacturer $manufacturer) => $manufacturer->toArray(), 
                    $this->manufacturers
                ),
                'count' => count($this->manufacturers),
            ],
        ];
    }

    /**
     * @return Manufacturer[]
     */
    public function getManufacturers(): array
    {
        return $this->manufacturers;
    }

    public function getCount(): int
    {
        return count($this->manufacturers);
    }
}