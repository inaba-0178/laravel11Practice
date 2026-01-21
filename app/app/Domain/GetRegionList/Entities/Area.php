<?php
namespace App\Domain\GetRegionList\Entities;

class Area
{
    private int $id;
    private string $name;
    private int $sortOrder;
    private array $regions;

    public function __construct(
        int $id,
        string $name,
        int $sortOrder,
        array $regions = []
    ){
        $this->id = $id;
        $this->name = $name;
        $this->sortOrder = $sortOrder;
        $this->regions = $regions;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function getRegions(): array
    {
        return $this->regions;
    }

    public function toArray(): array
    {
        $result = [
            'id' => $this->id,
            'name' => $this->name,
            'sort_order' => $this->sortOrder,
        ];

        if (!empty($this->regions)) {
            $result['regions'] = array_map(
                fn(Region $region) => $region->toArrayWithoutArea(), 
                $this->regions
            );
        }

        return $result;
    }
}