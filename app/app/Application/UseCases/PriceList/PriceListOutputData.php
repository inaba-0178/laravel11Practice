<?php

namespace App\Application\UseCases\PriceList;

use App\Domain\PriceList\Entities\Price;

class PriceListOutputData
{
    private ?array $prices;
    private int $count;

    /**
     * @param Price[] $prices
     */
    public function __construct(array $prices)
    {
        $this->prices = $prices;
        $this->count = count($prices);
    }

    public function toArray(): array
    {
        return [
            'success' => true,
            'data' => [
                'PriceList' => array_map(fn(Price $price) => $price->toArray(), $this->prices),
                'count' => $this->count,
            ],
        ];
    }

    public function Prices(): array
    {
        return $this->prices;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}