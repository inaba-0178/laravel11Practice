<?php
namespace App\Application\UseCases\SelectCarData;

use App\Domain\SelectCarData\Entities\Car;
use App\Domain\SelectCarData\Entities\CarDetail;

class SelectCarDataOutputData
{
    public function __construct(
        private readonly Car       $car,
        private readonly CarDetail $carDetail,
        private readonly array     $carImages,
        private readonly array     $carOptions,
        private readonly ?int      $totalPrice   = null,
        private readonly ?int      $priceWithTax = null,
        private readonly ?int      $miscFees     = null,
    ) {}

    public function toArray(): array
    {
        return [
            'success'       => true,
            'carData'       => array_merge($this->car->toArray(), [
                'totalPrice'   => $this->totalPrice,
                'priceWithTax' => $this->priceWithTax,
                'miscFees'     => $this->miscFees,
            ]),
            'carDetailData' => $this->carDetail->toArray(),
            'carImages'     => array_map(fn($image) => $image->toArray(), $this->carImages),
            'carOptions'    => array_map(fn($option) => $option->toArray(), $this->carOptions),
        ];
    }
}