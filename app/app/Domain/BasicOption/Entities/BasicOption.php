<?php

namespace App\Domain\BasicOption\Entities;

final class BasicOption
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $value,
        public readonly string  $label,
        public readonly bool    $isHighlight,
        public readonly int     $sortOrder,
        public readonly bool    $isActive,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getIsHighlight(): bool
    {
        return $this->isHighlight;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function toArray(): array
    {
        return [
            'value'         => $this->value,
            'label'         => $this->label,
            'is_highlight'  => $this->isHighlight,
        ];
    }
}