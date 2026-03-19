<?php

namespace App\Domain\SelectDealerData\Entities;

final class Dealer
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $name,
        public readonly ?string $postalCode,
        public readonly int     $regionId,
        public readonly string  $city,
        public readonly ?string $addressDetail,
        public readonly ?string $phone,
        public readonly ?string $businessHours,
        public readonly ?string $regularHoliday,
        public readonly ?float  $latitude,
        public readonly ?float  $longitude,
        public readonly ?float  $reviewRating,
        public readonly int     $reviewCount,
    ) {}

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getPostalCode(): ?string { return $this->postalCode; }
    public function getRegionId(): int { return $this->regionId; }
    public function getCity(): string { return $this->city; }
    public function getAddressDetail(): ?string { return $this->addressDetail; }
    public function getPhone(): ?string { return $this->phone; }
    public function getBusinessHours(): ?string { return $this->businessHours; }
    public function getRegularHoliday(): ?string { return $this->regularHoliday; }
    public function getLatitude(): ?float { return $this->latitude; }
    public function getLongitude(): ?float { return $this->longitude; }
    public function getReviewRating(): ?float { return $this->reviewRating; }
    public function getReviewCount(): int { return $this->reviewCount; }

    public function toArray(): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'postal_code'     => $this->postalCode ?? '',
            'city'            => $this->city,
            'address_detail'  => $this->addressDetail ?? '',
            'phone'           => $this->phone ?? '',
            'business_hours'  => $this->businessHours ?? '',
            'regular_holiday' => $this->regularHoliday ?? '',
            'latitude'        => $this->latitude,
            'longitude'       => $this->longitude,
            'review_rating'   => $this->reviewRating,
            'review_count'    => $this->reviewCount,
        ];
    }
}