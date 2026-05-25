<?php
namespace App\Domain\SelectManufacturerList\Entities;

final class CarSeriesImage
{
    public function __construct(
        private readonly int     $id,
        private readonly int     $seriesId,
        private readonly string  $filePath,
        private readonly string  $altText,
        private readonly int     $sortOrder,
        private readonly int     $isMain,
        private readonly int     $isActive,
    ) {}

    public function getId(): int { return $this->id; }
    public function getSeriesId(): int { return $this->seriesId; }
    public function getFilePath(): string { return $this->filePath; }
    public function getAltText(): string { return $this->altText; }
    public function getSortOrder(): int { return $this->sortOrder; }
    public function getIsMain(): int { return $this->isMain; }
    public function getIsActive(): int { return $this->isActive; }

    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'seriesId'  => $this->seriesId,
            'filePath'  => $this->filePath,
            'altText'   => $this->altText,
            'sortOrder' => $this->sortOrder,
            'isMain'    => $this->isMain,
            'isActive'  => $this->isActive,
        ];
    }
}