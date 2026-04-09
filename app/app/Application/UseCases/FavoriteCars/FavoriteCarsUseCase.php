<?php

namespace App\Application\UseCases\FavoriteCars;

use App\Domain\Common\Services\LoanPlanResolver;
use App\Domain\CarLoan\Services\LoanCalculator;
use App\Domain\FavoriteCars\Repositories\FavoriteCarsRepositoryInterface;
use App\Domain\FavoriteCars\Repositories\FavoriteCarSeriesRepositoryInterface;
use App\Domain\FavoriteCars\ValueObjects\CarIds;

class FavoriteCarsUseCase
{
    public function __construct(
        private readonly FavoriteCarsRepositoryInterface      $carsRepository,
        private readonly FavoriteCarSeriesRepositoryInterface $seriesRepository,
        private readonly LoanPlanResolver                     $loanPlanResolver,
    ) {}

    public function execute(CarIds $carIds): FavoriteCarsOutputData
    {
        $ids = $carIds->getValues();

        $cars       = $this->carsRepository->findByCarIds($ids);
        $mainImages = $this->carsRepository->findMainImagesByCarIds($ids);
        $loanPlans  = $this->loanPlanResolver->resolveByCarIds($ids);

        $seriesIds   = $cars->pluck('series_id')->unique()->toArray();
        $seriesNames = $this->seriesRepository->findSeriesNamesByIds($seriesIds);

        $sortedIds = array_flip($ids);

        $cars = $cars
            ->map(function ($car) use ($seriesNames, $mainImages, $loanPlans) {
                $plan         = $loanPlans[$car->id] ?? null;
                $months       = $plan ? collect($plan['months_options'])->first() : null;
                $loanMonthly  = ($plan && $months)
                    ? LoanCalculator::monthlyPayment($car->price, $plan['rate'], $months)
                    : null;

                return [
                    'id'             => $car->id,
                    'series_name'    => $seriesNames[$car->series_id] ?? null,
                    'price'          => $car->price,
                    'model_year'     => $car->model_year,
                    'mileage'        => $car->mileage,
                    'color'          => $car->color,
                    'status'         => $car->status,
                    'repair_history' => $car->repair_history,
                    'fuel_type'      => $car->fuel_type,
                    'transmission'   => $car->transmission,
                    'main_image_url' => $mainImages[$car->id]->image_url ?? null,
                    'loan_monthly'   => $loanMonthly,
                ];
            })
            ->sortBy(fn ($car) => $sortedIds[$car['id']] ?? PHP_INT_MAX)
            ->values();

        return new FavoriteCarsOutputData($cars);
    }
}