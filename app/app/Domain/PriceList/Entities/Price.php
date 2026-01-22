<?php

namespace App\Domain\PriceList\Entities;

class Price
{
    private int $id;
    private ?string $rangeName;
    private float $maxAmount;
    private bool $isUnlimited;

    public function __construct(
        int $id,
        ?string $rangeName,
        float $maxAmount,
        bool $isUnlimited,
    ) {
        $this->id = $id;
        $this->rangeName = $rangeName;
        $this->maxAmount= $maxAmount;
        $this->isUnlimited = $isUnlimited;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRangeName(): string
    {
        return $this->rangeName;
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
            'range_name' => $this->rangeName ?? '',
            'max_amount' => $this->maxAmount,
            'is_unlimited' => $this->isUnlimited,
        ];
    }
}