<?php

namespace App\Domain\FeaturedBrandList\Entities;

class Manufacturer
{
    private int $id;
    private ?string $name;
    private ?string $nameKana;
    private ?string $displayName;
    private ?string $code;
    private ?string $url;
    private ?string $description;
    private ?string $countryCode;
    private int $sortOrder;
    private int $isActive;

    public function __construct(
        int $id,
        ?string $name,
        ?string $nameKana,
        ?string $displayName,
        ?string $code,
        ?string $url,
        ?string $description,
        ?string $countryCode,
        int $sortOrder,
        int $isActive,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->nameKana = $nameKana;
        $this->displayName = $displayName;
        $this->code = $code;
        $this->url = $url;
        $this->description = $description;
        $this->countryCode = $countryCode;
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

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
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
            'displayName' => $this->displayName ?? '',
            'code' => $this->code ?? '',
            'url' => $this->url ?? '',
            'description' => $this->description ?? '',
            'countryCode' => $this->countryCode ?? '',
            'sortOrder' => $this->sortOrder,
            'isActive' => $this->isActive,
        ];
    }

}