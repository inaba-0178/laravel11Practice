<?php
namespace App\Application\UseCases\CarList;

use App\Domain\CarList\Entities\Car;
use App\Domain\CarList\Entities\CarDetail;

class CarListOutputData
{
    /**
     * @param Car[] $cars
     * @param CarDetail[] $carDetails
     */
    public function __construct(
        private readonly array $cars,
        private readonly array $carDetails,
    ) {}

    public function toArray(): array
    {
        // car_idをキーにしてCarDetailをマッピング
        $carDetailMap = [];
        foreach ($this->carDetails as $carDetail) {
            $carDetailMap[$carDetail->getCarId()] = $carDetail;
        }

        $carInfo = array_map(function (Car $car) use ($carDetailMap) {
            $detail = $carDetailMap[$car->getId()] ?? null;
            return array_merge(
                $car->toArray(),
                ['detail' => $detail?->toArray() ?? []]
            );
        }, $this->cars);

        return [
            'success' => true,
            'data'    => $carInfo,
        ];
    }

    public function getCars(): array
    {
        return $this->cars;
    }

    public function getCarDetails(): array
    {
        return $this->carDetails;
    }
}