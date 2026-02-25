<?php

namespace App\Domain\SelectManufacturerList\Entities;

final class Manufacturer
{
    private readonly int        $id;
    private readonly ?string    $name;
    private readonly ?string    $displayName;
    private readonly ?string    $url;
    private readonly ?string    $description;
    private readonly ?string    $countryCode;
    private readonly int        $isActive;

    public function __construct(
        int     $id,
        ?string $name,
        ?string $displayName,
        ?string $url,
        ?string $description,
        ?string $countryCode,
        int     $isActive,
    ) {
        $this->id           = $id;
        $this->name         = $name;
        $this->displayName  = $displayName;
        $this->url          = $url;
        $this->description  = $description;
        $this->countryCode  = $countryCode;
        $this->isActive     = $isActive;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
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
            'displayName'   => $this->displayName ?? '',
            'url'           => $this->url ?? '',
            'description'   => $this->description ?? '',
            'countryCode'   => $this->countryCode ?? '',
            'isActive'      => $this->isActive,
        ];
    }

}