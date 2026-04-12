<?php

declare(strict_types=1);

namespace App\Application\UseCases\SelectRegionCarList;

use App\Domain\Common\Entities\Car;
use App\Domain\Common\Services\CarMerger;

class SelectRegionCarListOutputData
{
    public function __construct(
        private readonly array              $carLists,
        private readonly int                $totalCount,
        private readonly array              $totalPrices    = [],
        private readonly array              $loanPlans      = [],
        private readonly ?CarMerger         $carMerger      = null,
    ) {}

    public function toArray(): array
    {
        return [
            'success'    => true,
            'carList'    => array_map(
                fn (Car $car) => $this->carMerger->merge($car, $this->totalPrices, $this->loanPlans),
                $this->carLists
            ),
            'count'      => count($this->carLists),
            'totalCount' => $this->totalCount,
        ];
    }
}