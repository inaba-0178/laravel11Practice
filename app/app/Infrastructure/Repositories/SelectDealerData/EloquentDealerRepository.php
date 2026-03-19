<?php

namespace App\Infrastructure\Repositories\SelectDealerData;

use App\Domain\SelectDealerData\Repositories\DealerRepositoryInterface;
use App\Domain\SelectDealerData\Entities\Dealer;
use App\Infrastructure\Eloquent\User\StkCarDealer;
use App\Infrastructure\Repositories\BaseRepository;
use RuntimeException;

class EloquentDealerRepository extends BaseRepository implements DealerRepositoryInterface
{
    public function __construct(StkCarDealer $model)
    {
        parent::__construct($model);
    }

    public function findById(int $dealerId): Dealer
    {
        $dealer = $this->model
            ->where('id', $dealerId)
            ->where('is_active', true)
            ->first();

        if ($dealer === null) {
            throw new RuntimeException("販売店ID '{$dealerId}' が見つかりませんでした。");
        }

        return $this->toEntity($dealer);
    }

    private function toEntity(StkCarDealer $model): Dealer
    {
        return new Dealer(
            id             : $model->id,
            name           : $model->name,
            postalCode     : $model->postal_code,
            regionId       : $model->region_id,
            city           : $model->city,
            addressDetail  : $model->address_detail,
            phone          : $model->phone,
            businessHours  : $model->business_hours,
            regularHoliday : $model->regular_holiday,
            latitude       : $model->latitude,
            longitude      : $model->longitude,
            reviewRating   : $model->review_rating,
            reviewCount    : $model->review_count ?? 0,
        );
    }
}