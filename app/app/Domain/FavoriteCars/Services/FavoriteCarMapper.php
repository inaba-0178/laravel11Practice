<?php

declare(strict_types=1);

namespace App\Domain\FavoriteCars\Services;

use App\Domain\Common\Services\CarListMapper;
use Illuminate\Support\Collection;

class FavoriteCarMapper
{
    public function __construct(
        private readonly CarListMapper $carListMapper,
    ) {}

    public function map(
        Collection $cars,
        array      $seriesNames,
        Collection $mainImages,
        array      $loanPlans,
        array      $totalPrices,
        array      $sortedIds,
    ): Collection {
        return $cars
            ->map(fn ($car) => $this->toArray(
                $car,
                $seriesNames,
                $mainImages,
                $loanPlans,
                $totalPrices,
            ))
            ->sortBy(fn ($car) => $sortedIds[$car['id']] ?? PHP_INT_MAX)
            ->values();
    }

    private function toArray(
        object     $car,
        array      $seriesNames,
        Collection $mainImages,
        array      $loanPlans,
        array      $totalPrices,
    ): array {
        return [
            'id'            => $car->id,
            'seriesName'    => $seriesNames[$car->series_id] ?? null,
            'price'         => $car->price,
            'totalPrice'    => $totalPrices[$car->id] ?? null,
            'modelYear'     => $car->model_year,
            'mileage'       => $car->mileage,
            'color'         => $car->color,
            'status'        => $car->status,
            'repairHistory' => $car->repair_history,
            'fuelType'      => $car->fuel_type,
            'transmission'  => $car->transmission,
            'mainImageUrl'  => $mainImages[$car->id]->image_url ?? null,
            'loanMonthly'   => $this->carListMapper->resolveLoanMonthly(
                (float) $car->price,
                $loanPlans,
                $car->id,
            ),
        ];
    }
}