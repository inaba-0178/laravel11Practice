<?php

declare(strict_types=1);

namespace App\Domain\SelectAreaCarList\Services;

use App\Domain\SelectAreaCarList\Entities\Car;
use App\Domain\Common\Services\CarListMapper;

class SelectAreaCarMerger
{
    public function __construct(
        private readonly CarListMapper $carListMapper,
    ) {}

    public function merge(Car $car, array $totalPrices, array $loanPlans): array
    {
        return array_merge($car->toArray(), [
            'totalPrice'  => $totalPrices[$car->id] ?? null,
            'loanMonthly' => $this->carListMapper->resolveLoanMonthly(
                $car->price,
                $loanPlans,
                $car->id,
            ),
        ]);
    }
}