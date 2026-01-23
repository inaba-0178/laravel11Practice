<?php

namespace App\Domain\PriceList\Entities;

class Price
{
    private int $id;
    private ?string $name;
    private float $maxAmount;
    private bool $isUnlimited;

    public function __construct(
        int $id,
        ?string $name,
        float $maxAmount,
        bool $isUnlimited,
    ) {
        $this->id = $id;
        $this->name = $name;
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
            'max_amount' => $this->maxAmount,
            'is_unlimited' => $this->isUnlimited,
        ];
    }
}