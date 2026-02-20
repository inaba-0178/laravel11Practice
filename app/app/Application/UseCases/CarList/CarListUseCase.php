<?php
namespace App\Application\UseCases\CarList;

use App\Domain\CarList\Repositories\CarRepositoryInterface;
use App\Domain\CarList\Repositories\CarDetailRepositoryInterface;
use App\Domain\CarList\ValueObjects\SeriesId;
use App\Domain\CarList\Exceptions\CarNotFoundException;

class CarListUseCase
{
    public function __construct(
        private readonly CarRepositoryInterface         $carRepository,
        private readonly CarDetailRepositoryInterface   $carDetailRepository,
    ) {}

    /**
     * 対象車両の中古車を取得する
     * 
     * @return CarListOutputData
     * @throws CarNotFoundException
     */
    public function execute(SeriesId $seriesId): CarListOutputData
    {
        $carCollection = $this->carRepository->findBySeriesId($seriesId->getValue());
        $ids = $carCollection->pluck('id')->all();
        $carDetailData = $this->carDetailRepository->findByCarId($ids);

        return new CarListOutputData($carCollection->all(), $carDetailData);
    }
}