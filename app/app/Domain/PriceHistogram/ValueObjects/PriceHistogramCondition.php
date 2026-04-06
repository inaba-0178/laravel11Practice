<?php

namespace App\Domain\PriceHistogram\ValueObjects;

class PriceHistogramCondition
{
    public function __construct(
        public readonly ?int $manufacturerId   = null,
        public readonly ?int $bodyTypeId       = null,
        public readonly ?int $priceFrom        = null,
        public readonly ?int $priceTo          = null,
        public readonly ?int $mileageFrom      = null,
        public readonly ?int $mileageTo        = null,
        public readonly ?int $ridingCapacity   = null,
        public readonly ?int $displacementFrom = null,
        public readonly ?int $displacementTo   = null,
        public readonly ?int $regionId         = null,
        public readonly ?int $vehicleId        = null,
    ) {}
}