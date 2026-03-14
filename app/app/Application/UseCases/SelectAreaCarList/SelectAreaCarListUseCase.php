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
     * @return SelectAreaCarListOutputData
     * @throws SelectAreaCarNotFoundException
     */
    public function execute(SeriesId $seriesId, RegionIds $regionIds, Offset $offset, Limit $limit): SelectAreaCarListOutputData
    {
        \Log::info($regionIds->getValue());
        $cars           = $this->carRepository->findBySeriesId($seriesId->getValue(), $regionIds->getValue(), $offset->getValue(), $limit->getValue());
        $totalCount     = $this->carRepository->findByTotalCount($seriesId->getValue(), $regionIds->getValue());
        return new SelectAreaCarListOutputData($cars, $totalCount);
    }
}