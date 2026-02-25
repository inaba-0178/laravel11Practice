<?php

namespace App\Domain\FeaturedBodyTypeList\Entities;

final class BodyType
{
    private readonly int     $id;
    private readonly ?string $name;
    private readonly ?string $nameKana;
    private readonly ?string $code;
    private readonly ?string $description;
    private readonly ?array  $availableCountries;
    private readonly int     $sortOrder;
    private readonly int     $isActive;

    public function __construct(
        int     $id,
        ?string $name,
        ?string $nameKana,
        ?string $code,
        ?string $description,
        ?array  $availableCountries,
        int     $sortOrder,
        int     $isActive,
    ) {
        $this->id                   = $id;
        $this->name                 = $name;
        $this->nameKana             = $nameKana;
        $this->code                 = $code;
        $this->description          = $description;
        $this->availableCountries   = $availableCountries;
        $this->sortOrder            = $sortOrder;
        $this->isActive             = $isActive;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getNameKana(): ?string
    {
        return $this->nameKana;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getAvailableCountries(): ?array
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
            'id'                    => $this->id,
            'name'                  => $this->name ?? '',
            'nameKana'              => $this->nameKana ?? '',
            'code'                  => $this->code ?? '',
            'description'           => $this->description ?? '',
            'availableCountries'    => $this->availableCountries ?? [],
            'sortOrder'             => $this->sortOrder,
            'isActive'              => $this->isActive,
        ];
    }

}