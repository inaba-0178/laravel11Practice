<?php

namespace App\Domain\ColorOption\Entities;

final class ColorOption
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $value,
        public readonly string  $label,
        public readonly ?string $hexCode,
        public readonly ?string $group,
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

    public function getHexCode(): ?string
    {
        return $this->hexCode;
    }

    public function getGroup(): ?string
    {
        return $this->group;
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
            'value'    => $this->value,
            'label'    => $this->label,
            'hex_code' => $this->hexCode,
            'group'    => $this->group,
        ];
    }
}