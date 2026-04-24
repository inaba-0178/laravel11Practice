<?php

declare(strict_types=1);

namespace App\Domain\SelectDealerAffiliatedData\Entities;

final class AffiliatedStore
{
    public function __construct(
        public readonly int     $id,
        public readonly int     $dealerId,
        public readonly string  $name,
        public readonly ?string $imageUrl,
        public readonly string  $address,
        public readonly ?string $phone,
        public readonly ?string $businessHoursFrom,
        public readonly ?string $businessHoursTo,
        public readonly ?string $regularHolidayDays,
        public readonly string  $type,
        public readonly string  $typeLabel,
    ) {}

    public function toArray(): array
    {
        return [
            'id'                 => $this->id,
            'dealerId'           => $this->dealerId,
            'name'               => $this->name,
            'imageUrl'           => $this->imageUrl,
            'address'            => $this->address,
            'phone'              => $this->phone,
            'businessHoursFrom'  => $this->businessHoursFrom,
            'businessHoursTo'    => $this->businessHoursTo,
            'regularHolidayDays' => $this->regularHolidayDays,
            'type'               => $this->type,
            'typeLabel'          => $this->typeLabel,
        ];
    }
}