<?php

namespace App\Application\UseCases\SelectAreaCarList;

use App\Domain\SelectAreaCarList\Entities\Car;
use App\Domain\SelectAreaCarList\Services\SelectAreaCarMerger;

class SelectAreaCarListOutputData
{
    public function __construct(
        private readonly array               $carLists,
        private readonly int                 $totalCount,
        private readonly array               $totalPrices      = [],
        private readonly array               $loanPlans        = [],
        private readonly ?SelectAreaCarMerger $selectAreaCarMerger = null,
    ) {}

    public function toArray(): array
    {
        return [
            'success'    => true,
            'carList'    => array_map(
                fn (Car $car) => $this->selectAreaCarMerger->merge($car, $this->totalPrices, $this->loanPlans),
                $this->carLists
            ),
            'count'      => count($this->carLists),
            'totalCount' => $this->totalCount,
        ];
    }
}