<?php
namespace App\Application\UseCases\SelectCarData;

use App\Domain\SelectCarData\Entities\Car;
use App\Domain\SelectCarData\Entities\CarDetail;

class SelectCarDataOutputData
{
    /**
     * @param Car       $car
     * @param CarDetail $carDetail
     */
    public function __construct(
        private readonly Car        $car,
        private readonly CarDetail  $carDetail,
        private readonly array      $carImages,
        private readonly array      $carOptions,
    ) {}

    public function toArray(): array
    {
        return [
            'success'       => true,
            'carData'       => $this->car,
            'carDetailData' => $this->carDetail,
            'carImages'     => $this->carImages,
            'carOptions'    => $this->carOptions,
        ];
    }

    public function getCar(): Car
    {
        return $this->car;
    }

    public function getCarDetail(): CarDetail
    {
        return $this->carDetail;
    }

    public function getCarImages(): array
    {
        return $this->carImages;
    }

    public function getCarOptions(): array
    {
        return $this->carOptions;
    }
}