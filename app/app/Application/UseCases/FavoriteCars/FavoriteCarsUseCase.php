<?php

namespace App\Application\UseCases\FavoriteCars;

use App\Domain\Common\Services\LoanPlanResolver;
use App\Domain\Common\Services\TotalPriceCalculator;
use App\Domain\CarLoan\Services\LoanCalculator;
use App\Domain\FavoriteCars\Repositories\FavoriteCarsRepositoryInterface;
use App\Domain\FavoriteCars\Repositories\FavoriteCarSeriesRepositoryInterface;
use App\Domain\FavoriteCars\ValueObjects\CarIds;
use App\Domain\FavoriteCars\Services\FavoriteCarMapper;

class FavoriteCarsUseCase
{
    public function __construct(
        private readonly FavoriteCarsRepositoryInterface      $carsRepository,
        private readonly FavoriteCarSeriesRepositoryInterface $seriesRepository,
        private readonly LoanPlanResolver                     $loanPlanResolver,
        private readonly TotalPriceCalculator                 $totalPriceCalculator,
        private readonly FavoriteCarMapper                    $mapper,
    ) {}

    public function execute(CarIds $carIds): FavoriteCarsOutputData
    {
        $ids = $carIds->getValues();

        $cars        = $this->carsRepository->findByCarIds($ids);
        $mainImages  = $this->carsRepository->findMainImagesByCarIds($ids);
        $loanPlans   = $this->loanPlanResolver->resolveByCarIds($ids);
        $seriesNames = $this->seriesRepository->findSeriesNamesByIds(
            $cars->pluck('series_id')->unique()->toArray()
        );
        $totalPrices = $this->totalPriceCalculator->calculateByCarIds($cars);
        $sortedIds   = array_flip($ids);

        $mapped = $this->mapper->map(
            $cars,
            $seriesNames,
            $mainImages,
            $loanPlans,
            $totalPrices,
            $sortedIds,
        );

        return new FavoriteCarsOutputData($mapped);
    }
}