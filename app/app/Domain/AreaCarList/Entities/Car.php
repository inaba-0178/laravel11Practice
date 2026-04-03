<?php
namespace App\Domain\AreaCarList\Entities;
use DateTimeImmutable;

final class Car
{
    public function __construct(
        public readonly int                 $id,
        public readonly ?int                $dealerId,
        public readonly int                 $seriesId,
        public readonly int                 $vehicleId,
        public readonly ?string             $stockNumber,
        public readonly string              $status,
        public readonly string              $price,
        public readonly string              $priceDisplayType,
        public readonly ?int                $modelYear,
        public readonly int                 $mileage,
        public readonly ?int                $bodyTypeId,
        public readonly string              $color,
        public readonly ?string             $transmission,
        public readonly ?string             $fuelType,
        public readonly int                 $regionId,
        public readonly string              $repairHistory,
        public readonly ?string             $mainImageUrl,
        public readonly ?DateTimeImmutable  $publishedAt,
        public readonly ?DateTimeImmutable  $soldAt,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getDealerId(): ?int
    {
        return $this->dealerId ?? 0;
    }

    public function getSeriesId(): int
    {
        return $this->seriesId;
    }

    public function getVehicleId(): int
    {
        return $this->vehicleId;
    }

    public function getStockNumber(): ?string
    {
        return $this->stockNumber;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function getModelYear(): ?int
    {
        return $this->modelYear;
    }

    public function getMileage(): int
    {
        return $this->mileage;
    }

    public function getBodyTypeId(): ?int
    {
        return $this->bodyTypeId;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getTransmission(): ?string
    {
        return $this->transmission;
    }

    public function getFuelType(): ?string
    {
        return $this->fuelType;
    }

    public function getRegionId(): int
    {
        return $this->regionId;
    }

    public function getRepairHistory(): string
    {
        return $this->repairHistory;
    }

    public function getMainImageUrl(): ?string
    {
        return $this->mainImageUrl;
    }

    public function getPublishedAt(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function getSoldAt(): ?DateTimeImmutable
    {
        return $this->soldAt;
    }

    public function toArray(): array
    {
        return [
            'id'                => $this->id,
            'dealerId'          => $this->dealerId,
            'seriesId'          => $this->seriesId,
            'vehicleId'         => $this->vehicleId,
            'stockNumber'       => $this->stockNumber ?? '',
            'status'            => $this->status,
            'price'             => $this->price,
            'priceDisplayType'  => $this->priceDisplayType,
            'modelYear'         => $this->modelYear,
            'mileage'           => $this->mileage,
            'bodyTypeId'        => $this->bodyTypeId,
            'color'             => $this->color,
            'transmission'      => $this->transmission ?? '',
            'fuelType'          => $this->fuelType ?? '',
            'regionId'          => $this->regionId,
            'repairHistory'     => $this->repairHistory,
            'mainImageUrl'      => $this->mainImageUrl ?? '',
            'publishedAt'       => $this->publishedAt?->format('Y-m-d H:i:s'),
            'soldAt'            => $this->soldAt?->format('Y-m-d H:i:s'),
        ];
    }
}