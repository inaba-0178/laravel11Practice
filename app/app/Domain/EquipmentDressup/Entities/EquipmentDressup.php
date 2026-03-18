<?php

namespace App\Domain\EquipmentDressup\Entities;

final class EquipmentDressup
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $value,
        public readonly string  $label,
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
        ];
    }
}