<?php

declare(strict_types=1);

namespace App\Application\UseCases\SelectRegionCarList;

use App\Domain\SelectRegionCarList\Repositories\CarRepositoryInterface;
use App\Domain\SelectRegionCarList\ValueObjects\RegionIds;
use App\Domain\SelectRegionCarList\ValueObjects\OffSet;
use App\Domain\SelectRegionCarList\ValueObjects\Limit;
use App\Domain\Common\Services\TotalPriceCalculator;
use App\Domain\Common\Services\LoanPlanResolver;
use App\Domain\Common\Services\CarMerger;

class SelectRegionCarListUseCase
{
    public function __construct(
        private readonly CarRepositoryInterface $carRepository,
        private readonly TotalPriceCalculator   $totalPriceCalculator,
        private readonly LoanPlanResolver       $loanPlanResolver,
        private readonly CarMerger              $carMerger,
    ) {}

    public function execute(
        RegionIds $regionIds,
        OffSet    $offset,
        Limit     $limit,
        array     $searchParams = [],
        string    $sortKey = '',
        string    $sortOrder = '',
    ): SelectRegionCarListOutputData {
        $cars = $this->carRepository->findByRegionIds(
            $regionIds->getValues(),
            $offset->getValue(),
            $limit->getValue(),
            $searchParams,
            $sortKey,
            $sortOrder,
        );

        $totalCount = $this->carRepository->findTotalCount(
            $regionIds->getValues(),
            $searchParams,
        );

        $carCollection = collect($cars)->map(fn ($car) => $car->toCalculatorInput());
        $totalPrices   = $this->totalPriceCalculator->calculateByCarIds($carCollection);
        $carIds        = collect($cars)->pluck('id')->toArray();
        $loanPlans     = $this->loanPlanResolver->resolveByCarIds($carIds);

        return new SelectRegionCarListOutputData(
            $cars,
            $totalCount,
            $totalPrices,
            $loanPlans,
            $this->carMerger,
        );
    }
}