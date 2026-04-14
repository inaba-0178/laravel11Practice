<?php

declare(strict_types=1);

namespace App\Domain\DealerImage\Entities;

final class DealerImage
{
    public function __construct(
        public readonly int     $id,
        public readonly int     $dealerId,
        public readonly string  $imagePath,
        public readonly ?string $altText,
        public readonly bool    $isMain,
        public readonly int     $sortOrder,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getDealerId(): int
    {
        return $this->dealerId;
    }

    public function getImagePath(): string
    {
        return $this->imagePath;
    }

    public function getAltText(): ?string
    {
        return $this->altText;
    }

    public function getIsMain(): bool
    {
        return $this->isMain;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'dealerId'  => $this->dealerId,
            'imagePath' => $this->imagePath,
            'altText'   => $this->altText,
            'isMain'    => $this->isMain,
            'sortOrder' => $this->sortOrder,
        ];
    }
}