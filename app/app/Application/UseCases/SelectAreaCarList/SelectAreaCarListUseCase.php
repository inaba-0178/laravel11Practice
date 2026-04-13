<?php

namespace App\Application\UseCases\SelectAreaCarList;

use App\Domain\SelectAreaCarList\Repositories\CarRepositoryInterface;
use App\Domain\SelectAreaCarList\ValueObjects\SeriesId;
use App\Domain\SelectAreaCarList\ValueObjects\RegionIds;
use App\Domain\SelectAreaCarList\ValueObjects\OffSet;
use App\Domain\SelectAreaCarList\ValueObjects\Limit;
use App\Domain\SelectAreaCarList\Exceptions\SelectAreaCarNotFoundException;
use App\Domain\Common\Services\TotalPriceCalculator;
use App\Domain\Common\Services\LoanPlanResolver;
use App\Domain\Common\Services\CarMerger;

class SelectAreaCarListUseCase
{
    public function __construct(
        private readonly CarRepositoryInterface $carRepository,
        private readonly TotalPriceCalculator   $totalPriceCalculator,
        private readonly LoanPlanResolver       $loanPlanResolver,
        private readonly CarMerger              $carMerger,
    ) {}

    /**
     * 対象車両の中古車を取得する
     *
     * @throws SelectAreaCarNotFoundException
     */
    public function execute(
        SeriesId    $seriesId,
        RegionIds   $regionIds,
        Offset      $offset,
        Limit       $limit,
        array       $searchParams   = [],
        string      $sortKey        = '',
        string      $sortOrder      = '',
    ): SelectAreaCarListOutputData {
        $cars = $this->carRepository->findBySeriesId(
            $seriesId->getValue(),
            $regionIds->getValue(),
            $offset->getValue(),
            $limit->getValue(),
            $searchParams,
            $sortKey,
            $sortOrder,
        );

        $totalCount = $this->carRepository->findByTotalCount(
            $seriesId->getValue(),
            $regionIds->getValue(),
            $searchParams,
        );

        // 支払総額・ローン一括計算
        $carCollection = collect($cars)->map(fn ($car) => $car->toCalculatorInput());
        
        $totalPrices   = $this->totalPriceCalculator->calculateByCarIds($carCollection);
        $carIds        = collect($cars)->pluck('id')->toArray();
        $loanPlans     = $this->loanPlanResolver->resolveByCarIds($carIds);

        return new SelectAreaCarListOutputData(
            $cars,
            $totalCount,
            $totalPrices,
            $loanPlans,
            $this->carMerger,
        );
    }
}