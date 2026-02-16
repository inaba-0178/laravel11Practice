<?php

namespace App\Domain\ManufacturerList\Entities;

final class Manufacturer  // finalを追加（エンティティは継承させない）
{
    public function __construct(
        private readonly int $id,
        private readonly ?string $name,
        private readonly ?string $nameKana,
        private readonly ?string $displayName,
        private readonly ?string $code,
        private readonly ?string $url,
        private readonly ?string $description,
        private readonly ?string $countryCode,
        private readonly int $sortOrder,
        private readonly int $isActive,
    ) {}

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
            'id'            => $this->id,
            'name'          => $this->name ?? '',
            'nameKana'      => $this->nameKana ?? '',
            'displayName'   => $this->displayName ?? '',
            'code'          => $this->code ?? '',
            'url'           => $this->url ?? '',
            'description'   => $this->description ?? '',
            'countryCode'   => $this->countryCode ?? '',
            'sortOrder'     => $this->sortOrder,
            'isActive'      => $this->isActive,
        ];
    }

}