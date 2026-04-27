<?php

declare(strict_types=1);

namespace App\Domain\SelectDealerList\Entities;

final class DealerListItem
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $name,
        public readonly ?string $imageUrl,
        public readonly string  $address,
        public readonly ?string $phone,
        public readonly ?string $businessHoursFrom,
        public readonly ?string $businessHoursTo,
        public readonly ?string $regularHolidayDays,
        public readonly ?float  $reviewRating,
        public readonly int     $reviewCount,
        public readonly ?float  $ratingService,
        public readonly ?float  $ratingAtmosphere,
        public readonly ?float  $ratingAfter,
        public readonly ?float  $ratingQuality,
        public readonly int     $carCount,
    ) {}

    public function toArray(): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'imageUrl'           => $this->imageUrl,
            'address'            => $this->address,
            'phone'              => $this->phone,
            'businessHoursFrom'  => $this->businessHoursFrom,
            'businessHoursTo'    => $this->businessHoursTo,
            'regularHolidayDays' => $this->regularHolidayDays,
            'reviewRating'       => $this->reviewRating,
            'reviewCount'        => $this->reviewCount,
            'ratingService'      => $this->ratingService,
            'ratingAtmosphere'   => $this->ratingAtmosphere,
            'ratingAfter'        => $this->ratingAfter,
            'ratingQuality'      => $this->ratingQuality,
            'carCount'           => $this->carCount,
        ];
    }
}