<?php

namespace App\Domain\AreaCarList\Entities;

final class Region
{
    private readonly int        $id;
    private readonly int        $areaCode;
    private readonly ?string    $name;
    private readonly ?string    $queryParam;
    private readonly int        $sortOrder;

    public function __construct(
        int     $id,
        int     $areaCode,
        ?string $name,
        ?string $queryParam,
        ?int    $sortOrder,
    ) {
        $this->id           = $id;
        $this->areaCode     = $areaCode;
        $this->name         = $name;
        $this->queryParam   = $queryParam;
        $this->sortOrder    = $sortOrder ?? 0;
    }

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAreaCode(): int
    {
        return $this->areaCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQueryParam(): string
    {
        return $this->queryParam;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'areacode'      => $this->name ?? '',
            'name'          => $this->name ?? '',
            'queryParam'    => $this->queryParam ?? '',
            'sortOrder'     => $this->sortOrder ?? 0,
        ];
    }
}