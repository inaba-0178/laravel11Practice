<?php

namespace App\Application\UseCases\SelectAreaCarList;

use App\Domain\SelectAreaCarList\Repositories\CarRepositoryInterface;
use App\Domain\SelectAreaCarList\ValueObjects\SeriesId;
use App\Domain\SelectAreaCarList\ValueObjects\RegionIds;
use App\Domain\SelectAreaCarList\ValueObjects\OffSet;
use App\Domain\SelectAreaCarList\ValueObjects\Limit;
use App\Domain\SelectAreaCarList\Exceptions\SelectAreaCarNotFoundException;

class SelectAreaCarListUseCase
{
    public function __construct(
        private readonly CarRepositoryInterface $carRepository,
    ) {}

    /**
     * 対象車両の中古車を取得する
     *
     * @throws SelectAreaCarNotFoundException
     */
    public function execute(
        SeriesId $seriesId,
        RegionIds $regionIds,
        Offset $offset,
        Limit $limit,
        array $searchParams = [],
        string $sortKey = '',
        string $sortOrder = '',
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

        return new SelectAreaCarListOutputData($cars, $totalCount);
    }
}