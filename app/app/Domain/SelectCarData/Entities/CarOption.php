<?php
namespace App\Domain\SelectCarData\Entities;

final class CarOption
{
    public function __construct(
        public readonly int     $id,
        public readonly int     $carId,
        public readonly ?string $optionCategory,
        public readonly ?string $optionName,
        public readonly ?int    $isEquipped,
        public readonly int     $displayOrder,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getCarId(): int
    {
        return $this->carId;
    }

    public function getOptionCategory(): ?string
    {
        return $this->optionCategory;
    }

    public function getOptionName(): ?string
    {
        return $this->optionName;
    }

    public function getIsEquipped(): ?int
    {
        return $this->isEquipped;
    }

    public function getDisplayOrder(): int
    {
        return $this->displayOrder;
    }

    public function toArray(): array
    {
        return [
            'carId'             => $this->carId,
            'optionCategory'    => $this->optionCategory,
            'optionName'        => $this->optionName,
            'isEquipped'        => $this->isEquipped,
            'displayOrder'      => $this->displayOrder,
        ];
    }
}