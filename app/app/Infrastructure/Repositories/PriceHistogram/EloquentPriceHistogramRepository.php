<?php

namespace App\Infrastructure\Repositories\PriceHistogram;

use App\Domain\PriceHistogram\Repositories\PriceHistogramRepositoryInterface;
use App\Domain\PriceHistogram\ValueObjects\PriceHistogramCondition;
use App\Domain\Common\Constants\PriceConstants;
use App\Infrastructure\Eloquent\User\StkCar;

class EloquentPriceHistogramRepository implements PriceHistogramRepositoryInterface
{
    /**
     * 価格帯ごとの車両件数を取得する
     */
    public function getHistogram(int $step, PriceHistogramCondition $condition): array
    {
        $stepInYen = $step * PriceConstants::MAN_EN;

        $query = StkCar::query()
            ->where('stk_cars.status', 'available')
            ->leftJoin('stk_car_details', 'stk_cars.id', '=', 'stk_car_details.car_id');

        $query = $this->applyConditions($query, $condition);

        $results = $query
            ->selectRaw("
                FLOOR(stk_cars.price / ?) * ? AS range_start,
                COUNT(*) AS count
            ", [$stepInYen, $stepInYen])
            ->groupBy('range_start')
            ->orderBy('range_start')
            ->get();

        $maxQuery = StkCar::query()
            ->where('stk_cars.status', 'available')
            ->leftJoin('stk_car_details', 'stk_cars.id', '=', 'stk_car_details.car_id');
        $maxQuery = $this->applyConditions($maxQuery, $condition);
        $maxPrice = $maxQuery->max('stk_cars.price') ?? 0;
        $maxInMan = (int) ceil($maxPrice / PriceConstants::MAN_EN);

        return [
            'step'      => $step,
            'max_price' => $maxInMan,
            'buckets'   => $results->map(fn ($row) => [
                'range_start' => (int) ($row->range_start / PriceConstants::MAN_EN),
                'range_end'   => (int) ($row->range_start / PriceConstants::MAN_EN) + $step,
                'count'       => (int) $row->count,
            ])->values()->toArray(),
        ];
    }

    /**
     * 検索条件を適用する
     */
    private function applyConditions($query, PriceHistogramCondition $condition)
    {
        if ($condition->manufacturerId !== null) {
            $query->where('stk_cars.manufacturer_id', $condition->manufacturerId);
        }
        if ($condition->bodyTypeId !== null) {
            $query->where('stk_cars.body_type_id', $condition->bodyTypeId);
        }
        if ($condition->priceFrom !== null) {
            $query->where('stk_cars.price', '>=', $condition->priceFrom * PriceConstants::MAN_EN);
        }
        if ($condition->priceTo !== null) {
            $query->where('stk_cars.price', '<=', $condition->priceTo * PriceConstants::MAN_EN);
        }
        if ($condition->mileageFrom !== null) {
            $query->where('stk_cars.mileage', '>=', $condition->mileageFrom);
        }
        if ($condition->mileageTo !== null) {
            $query->where('stk_cars.mileage', '<=', $condition->mileageTo);
        }
        if ($condition->ridingCapacity !== null) {
            $query->where('stk_car_details.riding_capacity', $condition->ridingCapacity);
        }
        if ($condition->displacementFrom !== null) {
            $query->where('stk_car_details.displacement', '>=', $condition->displacementFrom);
        }
        if ($condition->displacementTo !== null) {
            $query->where('stk_car_details.displacement', '<=', $condition->displacementTo);
        }
        if ($condition->regionId !== null) {
            $query->where('stk_cars.region_id', $condition->regionId);
        }
        if ($condition->vehicleId !== null) {
            $query->where('stk_cars.vehicle_id', $condition->vehicleId);
        }

        return $query;
    }
}