<?php

namespace App\Domain\FeaturedBrandList\Entities;

class FeaturedBrand
{
    private int $id;
    private ?string $manufacturerCode;
    private ?string $position;
    private int $sortOrder;
    private bool $isActive;

    public function __construct(
        int $id,
        ?string $manufacturerCode,
        ?string $position,
        int $sortOrder,
        bool $isActive,
    ) {
        $this->id = $id;
        $this->manufacturerCode = $manufacturerCode;
        $this->position= $position;
        $this->sortOrder = $sortOrder;
        $this->isActive = $isActive;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getManufacturerCode(): string
    {
        return $this->manufacturerCode;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'manufacturerCode' => $this->manufacturerCode ?? '',
            'position' => $this->position,
            'sortOrder' => $this->sortOrder,
            'isActive' => $this->isActive,
        ];
    }
}