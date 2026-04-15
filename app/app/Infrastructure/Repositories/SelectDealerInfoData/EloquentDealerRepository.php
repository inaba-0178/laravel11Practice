<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\SelectDealerInfoData;

use App\Domain\SelectDealerInfoData\Entities\Dealer;
use App\Domain\SelectDealerInfoData\Repositories\DealerRepositoryInterface;
use App\Domain\SelectDealerInfoData\Exceptions\DealerNotFoundException;
use App\Infrastructure\Eloquent\User\StkCarDealer;

class EloquentDealerRepository implements DealerRepositoryInterface
{
    public function __construct(
        private readonly StkCarDealer $model,
    ) {}

    /**
     * @throws DealerNotFoundException
     */
    public function findById(int $dealerId): Dealer
    {
        $dealer = $this->model
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->find($dealerId);

        if ($dealer === null) {
            throw new DealerNotFoundException($dealerId);
        }

        return $this->toEntity($dealer);
    }

    private function toEntity(StkCarDealer $model): Dealer
    {
        return new Dealer(
            id                          : $model->id,
            name                        : $model->name,
            postalCode                  : $model->postal_code,
            regionId                    : $model->region_id,
            areaCode                    : $model->area_code,
            city                        : $model->city,
            addressDetail               : $model->address_detail,
            phone                       : $model->phone,
            email                       : $model->email,
            websiteUrl                  : $model->website_url,
            businessHoursFrom           : $model->business_hours_from,
            businessHoursTo             : $model->business_hours_to,
            regularHolidayDays          : $model->regular_holiday_days,
            regularHolidayExceptHoliday : $model->regular_holiday_except_holiday,
            dealerType                  : $model->dealer_type,
            freeText                    : $model->free_text,
            latitude                    : $model->latitude,
            longitude                   : $model->longitude,
            isActive                    : $model->is_active,
            reviewRating                : $model->review_rating,
            reviewCount                 : $model->review_count,
        );
    }
}