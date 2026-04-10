<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Common;

use App\Domain\Common\Repositories\DealerFeeRepositoryInterface;
use App\Infrastructure\Eloquent\User\StkCar;
use App\Infrastructure\Eloquent\User\StkDealerFee;
use App\Infrastructure\Eloquent\Mst\MstVehicleWeightTax;
use App\Infrastructure\Eloquent\Mst\MstLiabilityInsurance;
use Illuminate\Support\Collection;

class EloquentDealerFeeRepository implements DealerFeeRepositoryInterface
{
    public function findByCarIds(array $carIds): Collection
    {
        if (empty($carIds)) {
            return collect();
        }

        // stk_carsのdealer_fee_idからStkDealerFeeを一括取得
        $cars = StkCar::whereIn('id', $carIds)
            ->whereNotNull('dealer_fee_id')
            ->with('dealerFee')
            ->get()
            ->keyBy('id');

        return $cars->map(fn ($car) => $car->dealerFee)->filter();
    }

    public function findByCarId(int $carId): ?object
    {
        $car = StkCar::find($carId);
        return $car?->dealerFee;
    }

    public function findWeightTax(int $weightKg, bool $isLight): ?object
    {
        return MstVehicleWeightTax::where('is_light', $isLight)
            ->where('weight_from', '<=', $weightKg)
            ->where('weight_to', '>', $weightKg)
            ->first();
    }

    public function findLiabilityInsurance(string $vehicleType, int $months): ?object
    {
        return MstLiabilityInsurance::where('vehicle_type', $vehicleType)
            ->where('months', $months)
            ->first();
    }
}