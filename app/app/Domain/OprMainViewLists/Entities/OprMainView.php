<?php

namespace App\Domain\OprMainViewLists\Entities;

final class OprMainView
{
    public function __construct(
        private readonly int     $id,
        private readonly ?string $title,
        private readonly ?string $sub,
        private readonly ?string $label,
        private readonly ?string $imagePath,
        private readonly ?string $linkUrl,
        private readonly int     $sortOrder,
        private readonly int     $isActive,
        private readonly ?string $startAt,
        private readonly ?string $endAt,
    ) {}

    public function getId(): int { return $this->id; }
    public function getTitle(): ?string { return $this->title; }
    public function getSub(): ?string { return $this->sub; }
    public function getLabel(): ?string { return $this->label; }
    public function getImagePath(): ?string { return $this->imagePath; }
    public function getLinkUrl(): ?string { return $this->linkUrl; }
    public function getSortOrder(): int { return $this->sortOrder; }
    public function getIsActive(): int { return $this->isActive; }
    public function getStartAt(): ?string { return $this->startAt; }
    public function getEndAt(): ?string { return $this->endAt; }

    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'title'     => $this->title,
            'sub'       => $this->sub,
            'label'     => $this->label,
            'imagePath' => $this->imagePath,
            'linkUrl'   => $this->linkUrl,
            'sortOrder' => $this->sortOrder,
            'isActive'  => $this->isActive,
            'startAt'   => $this->startAt,
            'endAt'     => $this->endAt,
        ];
    }
}