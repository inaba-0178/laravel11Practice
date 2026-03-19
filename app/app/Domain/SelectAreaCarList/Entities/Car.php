<?php

namespace App\Domain\SelectAreaCarList\Entities;

use DateTimeImmutable;

final class Car
{
    public function __construct(
        // stk_cars
        public readonly int                $id,
        public readonly ?int               $dealerId,
        public readonly int                $seriesId,
        public readonly int                $vehicleId,
        public readonly ?string            $stockNumber,
        public readonly string             $status,
        public readonly string             $price,
        public readonly string             $priceDisplayType,
        public readonly ?int               $modelYear,
        public readonly int                $mileage,
        public readonly ?int               $bodyTypeId,
        public readonly string             $color,
        public readonly ?string            $transmission,
        public readonly ?string            $fuelType,
        public readonly int                $regionId,
        public readonly string             $repairHistory,
        public readonly ?string            $mainImageUrl,
        public readonly ?DateTimeImmutable $publishedAt,
        public readonly ?DateTimeImmutable $soldAt,
        // stk_car_details
        public readonly ?string            $inspectionExpireDate,
        public readonly ?string            $inspectionStatus,
        public readonly ?string            $driveSystem,
        public readonly ?int               $displacement,
        public readonly ?string            $steeringWheel,
        public readonly ?int               $numberOfDoors,
        public readonly ?string            $slideDoor,
        public readonly ?int               $ridingCapacity,
        // stk_car_dealers
        public readonly ?string            $dealerName,
        public readonly ?string            $dealerCity,
        public readonly ?float             $dealerRating,
        public readonly ?int               $dealerReviewCount,
        // mst_body_types
        public readonly ?string            $bodyTypeName,
        // 新着フラグ（publishedAtが7日以内）
        public readonly bool               $isNew,
    ) {}

    public function toArray(): array
    {
        return [
            'id'                  => $this->id,
            'dealerId'            => $this->dealerId,
            'seriesId'            => $this->seriesId,
            'vehicleId'           => $this->vehicleId,
            'stockNumber'         => $this->stockNumber ?? '',
            'status'              => $this->status,
            'price'               => $this->price,
            'priceDisplayType'    => $this->priceDisplayType,
            'modelYear'           => $this->modelYear,
            'mileage'             => $this->mileage,
            'bodyTypeId'          => $this->bodyTypeId,
            'bodyTypeName'        => $this->bodyTypeName ?? '',
            'color'               => $this->color,
            'transmission'        => $this->transmission ?? '',
            'fuelType'            => $this->fuelType ?? '',
            'regionId'            => $this->regionId,
            'repairHistory'       => $this->repairHistory,
            'mainImageUrl'        => $this->mainImageUrl ?? '',
            'publishedAt'         => $this->publishedAt?->format('Y-m-d H:i:s'),
            'soldAt'              => $this->soldAt?->format('Y-m-d H:i:s'),
            'inspectionExpireDate'=> $this->inspectionExpireDate ?? '',
            'inspectionStatus'    => $this->inspectionStatus ?? '',
            'driveSystem'         => $this->driveSystem ?? '',
            'displacement'        => $this->displacement,
            'steeringWheel'       => $this->steeringWheel ?? '',
            'numberOfDoors'       => $this->numberOfDoors,
            'slideDoor'           => $this->slideDoor ?? '',
            'ridingCapacity'      => $this->ridingCapacity,
            'dealerName'          => $this->dealerName ?? '',
            'dealerCity'          => $this->dealerCity ?? '',
            'dealerRating'        => $this->dealerRating,
            'dealerReviewCount'   => $this->dealerReviewCount,
            'isNew'               => $this->isNew,
        ];
    }
}