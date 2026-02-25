<?php

namespace App\Domain\RegionList\Entities;

final class Region
{
    private readonly int        $id;
    private readonly ?string    $name;
    private readonly ?string    $queryParam;
    private readonly int        $sortOrder;
    private readonly ?Area      $area;

    public function __construct(
        int     $id,
        ?string $name,
        ?string $queryParam,
        ?int    $sortOrder,
        ?Area   $area = null
    ) {
        $this->id           = $id;
        $this->name         = $name;
        $this->queryParam   = $queryParam;
        $this->sortOrder    = $sortOrder ?? 0;
        $this->area         = $area;
    }

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getQueryParam(): ?string
    {
        return $this->queryParam;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name ?? '',
            'query_param'   => $this->queryParam ?? '',
            'sort_order'    => $this->sortOrder ?? 0,
            'area'          => $this->area?->toArray(),
        ];
    }

    //area情報を含めない版
    public function toArrayWithoutArea(): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name ?? '',
            'query_param'   => $this->queryParam ?? '',
            'sort_order'    => $this->sortOrder,
        ];
    }
}