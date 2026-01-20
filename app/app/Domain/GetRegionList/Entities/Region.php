<?php

namespace App\Domain\GetRegionList\Entities;

class Region
{
    private int $id;
    private ?string $name;
    private ?string $queryParam;
    private int $sortOrder;

    public function __construct(
        int $id,
        ?string $name,
        ?string $queryParam,
        ?int $sortOrder
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->queryParam = $queryParam;
        $this->sortOrder = $sortOrder ?? 0;
    }

    public function getId(): int
    {
        return $this->id;
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
            'id' => $this->id,
            'name' => $this->name ?? '',
            'query_param' => $this->queryParam ?? '',
            'sort_order' => $this->sortOrder ?? 0,
        ];
    }
}