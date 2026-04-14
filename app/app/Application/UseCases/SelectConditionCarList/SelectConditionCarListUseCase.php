<?php

declare(strict_types=1);

namespace App\Application\UseCases\SelectConditionCarList;

use App\Domain\SelectConditionCarList\Repositories\CarRepositoryInterface;
use App\Domain\Common\ValueObjects\OffSet;
use App\Domain\Common\ValueObjects\Limit;
use App\Domain\Common\Services\TotalPriceCalculator;
use App\Domain\Common\Services\LoanPlanResolver;
use App\Domain\Common\Services\CarMerger;

class SelectConditionCarListUseCase
{
    public function __construct(
        private readonly CarRepositoryInterface $carRepository,
        private readonly TotalPriceCalculator   $totalPriceCalculator,
        private readonly LoanPlanResolver       $loanPlanResolver,
        private readonly CarMerger              $carMerger,
    ) {}

    public function execute(
        OffSet $offset,
        Limit  $limit,
        array  $searchParams = [],
        string $sortKey = '',
        string $sortOrder = '',
    ): SelectConditionCarListOutputData {
        $cars = $this->carRepository->findByCondition(
            $offset->getValue(),
            $limit->getValue(),
            $searchParams,
            $sortKey,
            $sortOrder,
        );

        $totalCount = $this->carRepository->findTotalCount($searchParams);

        $carCollection = collect($cars)->map(fn ($car) => $car->toCalculatorInput());
        $totalPrices   = $this->totalPriceCalculator->calculateByCarIds($carCollection);
        $carIds        = collect($cars)->pluck('id')->toArray();
        $loanPlans     = $this->loanPlanResolver->resolveByCarIds($carIds);

        return new SelectConditionCarListOutputData(
            $cars,
            $totalCount,
            $totalPrices,
            $loanPlans,
            $this->carMerger,
        );
    }
}