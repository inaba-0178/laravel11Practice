<?php

declare(strict_types=1);

namespace App\Domain\SelectDealerInfoData\Entities;

final class Dealer
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $name,
        public readonly ?string $postalCode,
        public readonly ?int    $regionId,
        public readonly ?int    $areaCode,
        public readonly string  $city,
        public readonly ?string $addressDetail,
        public readonly ?string $phone,
        public readonly ?string $email,
        public readonly ?string $websiteUrl,
        public readonly ?string $businessHoursFrom,
        public readonly ?string $businessHoursTo,
        public readonly ?string $regularHolidayDays,
        public readonly bool    $regularHolidayExceptHoliday,
        public readonly string  $dealerType,
        public readonly ?string $freeText,
        public readonly ?float  $latitude,
        public readonly ?float  $longitude,
        public readonly bool    $isActive,
        public readonly ?float  $reviewRating,
        public readonly int     $reviewCount,
    ) {}

    public function toArray(): array
    {
        return [
            'id'                          => $this->id,
            'name'                        => $this->name,
            'postalCode'                  => $this->postalCode,
            'regionId'                    => $this->regionId,
            'areaCode'                    => $this->areaCode,
            'city'                        => $this->city,
            'addressDetail'               => $this->addressDetail,
            'phone'                       => $this->phone,
            'email'                       => $this->email,
            'websiteUrl'                  => $this->websiteUrl,
            'businessHoursFrom'           => $this->businessHoursFrom,
            'businessHoursTo'             => $this->businessHoursTo,
            'regularHolidayDays'          => $this->regularHolidayDays,
            'regularHolidayExceptHoliday' => $this->regularHolidayExceptHoliday,
            'dealerType'                  => $this->dealerType,
            'freeText'                    => $this->freeText,
            'latitude'                    => $this->latitude,
            'longitude'                   => $this->longitude,
            'isActive'                    => $this->isActive,
            'reviewRating'                => $this->reviewRating,
            'reviewCount'                 => $this->reviewCount,
        ];
    }
}