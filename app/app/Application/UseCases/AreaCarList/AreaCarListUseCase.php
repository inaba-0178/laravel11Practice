<?php
namespace App\Application\UseCases\AreaCarList;

use App\Domain\AreaCarList\Repositories\CarRepositoryInterface;
use App\Domain\AreaCarList\Repositories\AreaRepositoryInterface;
use App\Domain\AreaCarList\Repositories\RegionRepositoryInterface;
use App\Domain\AreaCarList\Services\AreaCarListDomainService;
use App\Domain\AreaCarList\ValueObjects\SeriesId;
use App\Domain\AreaCarList\Exceptions\AreaCarNotFoundException;

class AreaCarListUseCase
{
    public function __construct(
        private readonly CarRepositoryInterface     $carRepository,
        private readonly AreaRepositoryInterface    $areaRepository,
        private readonly RegionRepositoryInterface  $regionRepository,
        private readonly AreaCarListDomainService   $areaCarListDomainService,
    ) {}

    /**
     * 対象車両の中古車を取得する
     * 
     * @return AreaCarListOutputData
     * @throws AreaCarNotFoundException
     */
    public function execute(SeriesId $seriesId): AreaCarListOutputData
    {
        $cars = $this->carRepository->findBySeriesId($seriesId->getValue());
        $areas = $this->areaRepository->findAll();
        $regions = $this->regionRepository->findAll();
        $areaCarList = $this->areaCarListDomainService->build($areas, $regions, $cars); 
        
        return new AreaCarListOutputData($areaCarList);
    }
}