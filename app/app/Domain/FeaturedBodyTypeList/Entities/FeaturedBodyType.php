<?php

namespace App\Domain\FeaturedBodyTypeList\Entities;

final class FeaturedBodyType
{
    private readonly int        $id;
    private readonly ?string    $bodyTypeCode;
    private readonly ?string    $position;
    private readonly int        $sortOrder;
    private readonly int        $isActive;

    public function __construct(
        int     $id,
        ?string $bodyTypeCode,
        ?string $position,
        int     $sortOrder,
        int     $isActive,
    ) {
        $this->id           = $id;
        $this->bodyTypeCode = $bodyTypeCode;
        $this->position     = $position;
        $this->sortOrder    = $sortOrder;
        $this->isActive     = $isActive;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getBodyTypeCode(): ?string
    {
        return $this->bodyTypeCode;
    }

    public function getPosition(): ?string
    {
        return $this->position;
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
            'bodyTypeCode'  => $this->bodyTypeCode ?? '',
            'position'      => $this->position,
            'sortOrder'     => $this->sortOrder,
            'isActive'      => $this->isActive,
        ];
    }
}