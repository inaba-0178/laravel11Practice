<?php
namespace App\Application\UseCases\SelectCarData;

use App\Domain\SelectCarData\Repositories\CarRepositoryInterface;
use App\Domain\SelectCarData\Repositories\CarDetailRepositoryInterface;
use App\Domain\SelectCarData\Repositories\CarImageRepositoryInterface;
use App\Domain\SelectCarData\Repositories\CarOptionRepositoryInterface;
use App\Domain\SelectCarData\ValueObjects\CarId;
use App\Domain\SelectCarData\Exceptions\CarNotFoundException;
use App\Domain\Common\Services\TotalPriceCalculator;

class SelectCarDataUseCase
{
    public function __construct(
        private readonly CarRepositoryInterface         $carRepository,
        private readonly CarDetailRepositoryInterface   $carDetailRepository,
        private readonly CarImageRepositoryInterface    $carImageRepository,
        private readonly CarOptionRepositoryInterface   $carOptionRepository,
        private readonly TotalPriceCalculator           $totalPriceCalculator,
    ) {}

    /**
     * 対象車両の中古車を取得する
     * 
     * @return SelectCarDataOutputData
     * @throws CarNotFoundException
     */
    public function execute(CarId $carId): SelectCarDataOutputData
    {
        $carData = $this->carRepository->findById($carId->getValue());
        
        $id = $carData->getId();

        $carDetailData = $this->carDetailRepository->findByCarId($id);
        $carImageData = $this->carImageRepository->findByCarId($id);
        $carOptionData = $this->carOptionRepository->findByCarId($id);

        $carInput = $carData->toCalculatorInput(
            $carDetailData->inspectionExpireDate ?? null
        );

        $totalPrice   = $this->totalPriceCalculator->calculate($carInput);
        $priceWithTax = $this->totalPriceCalculator->calcPriceWithTax((int)$carData->price);
        $miscFees     = $this->totalPriceCalculator->calcMiscFees($carInput);

        return new SelectCarDataOutputData(
            $carData,
            $carDetailData,
            $carImageData,
            $carOptionData,
            $totalPrice,
            $priceWithTax,
            $miscFees,
        );
    }
}