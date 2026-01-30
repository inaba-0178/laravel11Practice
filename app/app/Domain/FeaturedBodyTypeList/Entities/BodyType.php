<?php

namespace App\Domain\FeaturedBodyTypeList\Entities;

class BodyType
{
    private int $id;
    private ?string $name;
    private ?string $nameKana;
    private ?string $code;
    private ?string $description;
    private ?text $availableCountries;
    private int $sortOrder;
    private int $isActive;

    public function __construct(
        int $id,
        ?string $name,
        ?string $nameKana,
        ?string $code,
        ?string $description,
        ?text $availableCountries,
        int $sortOrder,
        int $isActive,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->nameKana = $nameKana;
        $this->code = $code;
        $this->description = $description;
        $this->availableCountries = $availableCountries;
        $this->sortOrder = $sortOrder;
        $this->isActive = $isActive;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNameKana(): string
    {
        return $this->nameKana;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getAvailableCountries(): ?text
    {
        return $this->availableCountries;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function getIsActive(): int
    {
        return $this->isActive;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? '',
            'nameKana' => $this->nameKana ?? '',
            'code' => $this->code ?? '',
            'description' => $this->description ?? '',
            'available_countries' => $this->availableCountries ?? '',
            'sortOrder' => $this->sortOrder,
            'isActive' => $this->isActive,
        ];
    }

}