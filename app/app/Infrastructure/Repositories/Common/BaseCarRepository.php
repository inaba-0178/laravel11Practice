<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Common;

use App\Domain\Common\Entities\Car;
use App\Infrastructure\Eloquent\User\StkCar;
use Illuminate\Database\Eloquent\Builder;
use App\Domain\Common\Constants\PriceConstants;
use App\Domain\Common\Constants\CountryCode;
use Illuminate\Support\Collection;
use App\Infrastructure\Repositories\BaseRepository;
use DateTimeImmutable;
use App\Infrastructure\Eloquent\Mst\MstBodyTypes;

abstract class BaseCarRepository extends BaseRepository
{
    protected function applySearchFilters(Builder $query, array $searchParams): void
    {
        if (!empty($searchParams['priceFrom'])) {
            $query->where('stk_cars.price', '>=', $searchParams['priceFrom'] * PriceConstants::MAN_EN);
        }
        if (!empty($searchParams['priceTo'])) {
            $query->where('stk_cars.price', '<=', $searchParams['priceTo'] * PriceConstants::MAN_EN);
        }
        if (!empty($searchParams['yearFrom'])) {
            $query->where('stk_cars.model_year', '>=', $searchParams['yearFrom']);
        }
        if (!empty($searchParams['yearTo'])) {
            $query->where('stk_cars.model_year', '<=', $searchParams['yearTo']);
        }
        if (!empty($searchParams['mileageFrom'])) {
            $query->where('stk_cars.mileage', '>=', $searchParams['mileageFrom']);
        }
        if (!empty($searchParams['mileageTo'])) {
            $query->where('stk_cars.mileage', '<=', $searchParams['mileageTo']);
        }
        if (!empty($searchParams['transmission'])) {
            $query->whereIn('stk_cars.transmission', explode(',', $searchParams['transmission']));
        }
        if (!empty($searchParams['engineType'])) {
            $query->where('stk_cars.fuel_type', $searchParams['engineType']);
        }
        if (!empty($searchParams['options']) && in_array('no_repair', explode(',', $searchParams['options']))) {
            $query->where('stk_cars.repair_history', 'none');
        }
        if (!empty($searchParams['colors'])) {
            $query->whereIn('stk_cars.color', explode(',', $searchParams['colors']));
        }
        if (!empty($searchParams['engineFrom'])) {
            $query->where('stk_car_details.displacement', '>=', $searchParams['engineFrom']);
        }
        if (!empty($searchParams['engineTo'])) {
            $query->where('stk_car_details.displacement', '<=', $searchParams['engineTo']);
        }
        if (!empty($searchParams['driveType'])) {
            $query->where('stk_car_details.drive_system', $searchParams['driveType']);
        }
        if (!empty($searchParams['handle'])) {
            $query->where('stk_car_details.steering_wheel', $searchParams['handle']);
        }
        if (!empty($searchParams['doorCount'])) {
            $query->where('stk_car_details.number_of_doors', $searchParams['doorCount']);
        }
        if (!empty($searchParams['slideDoor'])) {
            $query->where('stk_car_details.slide_door', $searchParams['slideDoor']);
        }
        if (!empty($searchParams['passengerCount'])) {
            $query->where('stk_car_details.riding_capacity', $searchParams['passengerCount']);
        }
        if (!empty($searchParams['inspectionRemaining'])) {
            $date = match($searchParams['inspectionRemaining']) {
                '6m'    => now()->addMonths(6)->format('Y-m-d'),
                '1y'    => now()->addYear()->format('Y-m-d'),
                '2y'    => now()->addYears(2)->format('Y-m-d'),
                default => null,
            };
            if ($date) {
                $query->where('stk_car_details.inspection_expire_date', '>=', $date);
            }
        }
        if (!empty($searchParams['freeWord'])) {
            $query->where('stk_car_details.free_text', 'like', '%' . $searchParams['freeWord'] . '%');
        }
        if (!empty($searchParams['carTypes'])) {
            $carTypes = explode(',', $searchParams['carTypes']);
            $query->whereHas('vehicle', function ($q) use ($carTypes) {
                if (in_array('domestic', $carTypes)) $q->where('country_code', CountryCode::JP);
                if (in_array('import', $carTypes)) $q->where('country_code', '!=', CountryCode::JP);
            });
            $specialTypes = ['welfare', 'cold_region', 'camping', 'commercial', 'reimport'];
            $hasSpecial   = array_intersect($carTypes, $specialTypes);
            if (!empty($hasSpecial)) {
                $query->whereHas('options', function ($q) use ($hasSpecial) {
                    $q->where('option_category', 'special_type')
                      ->whereIn('option_name', $hasSpecial)
                      ->where('is_equipped', true);
                });
            }
        }
        if (!empty($searchParams['dealerId'])) {
            $query->where('stk_cars.dealer_id', (int) $searchParams['dealerId']);
        }
    }

    protected function applySorting(Builder $query, string $sortKey, string $sortOrder): void
    {
        $order = $sortOrder === 'asc' ? 'asc' : 'desc';
        match($sortKey) {
            'publishedAt'   => $query->orderBy('stk_cars.published_at', $order),
            'price'         => $query->orderBy('stk_cars.price', $order),
            'modelYear'     => $query->orderBy('stk_cars.model_year', $order),
            'mileage'       => $query->orderBy('stk_cars.mileage', $order),
            'displacement'  => $query->orderBy('stk_car_details.displacement', $order),
            'repairHistory' => $query->orderBy('stk_cars.repair_history', $order),
            'inspection'    => $query->orderBy('stk_car_details.inspection_expire_date', $order),
            default         => $query->orderBy('stk_cars.published_at', 'desc'),
        };
    }

    protected function toEntity(StkCar $model, $bodyTypes = null): Car
    {
        $publishedAt = $model->published_at ? $model->published_at->toDateTimeImmutable() : null;

        $soldAt             = $model->sold_at ? $model->sold_at->toDateTimeImmutable() : null;
        $isNew              = $publishedAt ? $publishedAt >= new DateTimeImmutable('-7 days') : false;
        $dealerRating       = $model->dealer_rating ? (float)$model->dealer_rating : null;
        $dealerReviewCount  = $model->dealer_review_count ?? 0;
        $bodyTypeName       = isset($bodyTypes[$model->body_type_id]) ? $bodyTypes[$model->body_type_id]->name : '';
        $recycleFee         = $model->recycle_fee ? (int)$model->recycle_fee : null;
        $dealerFeeId        = $model->dealer_fee_id ? (int)$model->dealer_fee_id : null;

        return new Car(
            id                   : $model->id,
            dealerId             : $model->dealer_id,
            seriesId             : $model->series_id,
            vehicleId            : $model->vehicle_id,
            stockNumber          : $model->stock_number,
            status               : $model->status,
            price                : $model->price,
            priceDisplayType     : $model->price_display_type,
            modelYear            : $model->model_year,
            mileage              : $model->mileage,
            bodyTypeId           : $model->body_type_id,
            color                : $model->color,
            transmission         : $model->transmission,
            fuelType             : $model->fuel_type,
            regionId             : $model->region_id,
            repairHistory        : $model->repair_history,
            mainImageUrl         : $model->image_url,
            publishedAt          : $publishedAt,
            soldAt               : $soldAt,
            inspectionExpireDate : $model->inspection_expire_date,
            inspectionStatus     : $model->inspection_status,
            driveSystem          : $model->drive_system,
            displacement         : $model->displacement,
            steeringWheel        : $model->steering_wheel,
            numberOfDoors        : $model->number_of_doors,
            slideDoor            : $model->slide_door,
            ridingCapacity       : $model->riding_capacity,
            dealerName           : $model->dealer_name,
            dealerCity           : $model->dealer_city,
            dealerRating         : $dealerRating,
            dealerReviewCount    : $dealerReviewCount,
            bodyTypeName         : $bodyTypeName,
            isNew                : $isNew,
            recycleFee           : $recycleFee,
            dealerFeeId          : $dealerFeeId,
            totalPrice           : null,
            loanMonthly          : null,
        );
    }

    protected function getBodyTypes(Collection $cars): Collection
    {
        $bodyTypeIds = $cars->pluck('body_type_id')->filter()->unique()->toArray();
        return MstBodyTypes::whereIn('id', $bodyTypeIds)
            ->get()
            ->keyBy('id');
    }
}