<?php

namespace App\Application\UseCases\PriceHistogram;

use App\Domain\Common\Constants\PriceHistogramConstants;

class PriceHistogramOutputData
{
    public function __construct(
        private readonly array $histogram,
    ) {}

    public function toArray(): array
    {
        return [
            'success'          => true,
            'histogram'        => $this->histogram,
            'price_slider_max' => PriceHistogramConstants::PRICE_SLIDER_MAX,
        ];
    }
}