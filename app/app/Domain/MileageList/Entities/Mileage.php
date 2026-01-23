<?php

namespace App\Domain\MileageList\Entities;

class Mileage
{
    private int $id;
    private ?string $name;
    private ?float $minAmount;
    private ?float $maxAmount;
    private bool $isUnlimited;

    public function __construct(
        int $id,
        ?string $name,
        ?float $minAmount,
        ?float $maxAmount,
        bool $isUnlimited,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->minAmount= $minAmount;
        $this->maxAmount= $maxAmount;
        $this->isUnlimited = $isUnlimited;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMinAmount(): float
    {
        return $this->minAmount;
    }

    public function getMaxAmount(): float
    {
        return $this->maxAmount;
    }

    public function getIsUnlimited(): bool
    {
        return $this->isUnlimited;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? '',
            'min_amount' => $this->minAmount,
            'max_amount' => $this->maxAmount,
            'is_unlimited' => $this->isUnlimited,
        ];
    }
}