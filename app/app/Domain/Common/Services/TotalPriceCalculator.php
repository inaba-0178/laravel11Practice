<?php

declare(strict_types=1);

namespace App\Domain\Common\Services;

use App\Domain\Common\Repositories\DealerFeeRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstBodyTypes;
use App\Infrastructure\Eloquent\Mst\MstVehicleYearVersions;
use App\Domain\Common\Constants\InsuranceConstants;
use App\Domain\Common\Constants\TaxConstants;
use Illuminate\Support\Collection;
use App\Infrastructure\Eloquent\Mst\MstVehicleTax;

class TotalPriceCalculator
{

    public function __construct(
        private readonly DealerFeeRepositoryInterface $repository,
    ) {}

    /**
     * 単一車両の支払総額を計算
     */
    public function calculate(object $car): ?int
    {
        $price = (int) $car->price;

        // 消費税
        $consumptionTax = (int) round($price * TaxConstants::CONSUMPTION_TAX_RATE);

        // 車両本体価格（税込）
        $priceWithTax = $price + $consumptionTax;

        // 諸費用
        $miscFees = $this->calcMiscFees($car);

        return $priceWithTax + $miscFees;
    }

    /**
     * 車両本体価格（税込）を計算
     */
    public function calcPriceWithTax(int $price): int
    {
        return $price + (int) round($price * TaxConstants::CONSUMPTION_TAX_RATE);
    }

    /**
     * 諸費用合計を計算
     */
    public function calcMiscFees(object $car): int
    {
        // リサイクル預託金
        $recycleFee = (int) ($car->recycle_fee ?? 0);

        // ディーラー諸費用
        $dealerFee   = $this->repository->findByCarId($car->id);
        $dealerTotal = $dealerFee
            ? (int)$dealerFee->registration_fee
                + (int)$dealerFee->garage_cert_fee
                + (int)$dealerFee->delivery_fee
                + (int)$dealerFee->maintenance_fee
            : 0;

        // 軽自動車判定
        $isLight     = $this->isLightVehicle($car->body_type_id);
        $vehicleType = $isLight ? 'light' : 'standard';

        // 自動車重量税
        $weightTax = $this->resolveWeightTax($car, $isLight);

        // 自賠責保険料
        $liabilityInsurance = $this->resolveLiabilityInsurance($car, $vehicleType);

        $vehicleTax = $this->resolveVehicleTax($car);
        return $recycleFee + $dealerTotal + $weightTax + $liabilityInsurance + $vehicleTax;
    }

    /**
     * 複数車両の支払総額を一括計算
     */
    public function calculateByCarIds(Collection $cars): array
    {
        $result = [];
        foreach ($cars as $car) {
            $result[$car->id] = $this->calculate($car);
        }
        return $result;
    }

    /**
     * 軽自動車判定
     */
    public function isLightVehicle(?int $bodyTypeId): bool
    {
        if (!$bodyTypeId) return false;
        $bodyType = MstBodyTypes::find($bodyTypeId);
        return $bodyType?->code === 'KeiCars';
    }

    /**
     * 自動車重量税を解決
     */
    public function resolveWeightTax(object $car, bool $isLight): int
    {
        if (!$car->vehicle_id || !$car->model_year) return 0;

        $version = MstVehicleYearVersions::where('vehicle_id', $car->vehicle_id)
            ->where('year_from', '<=', $car->model_year)
            ->where('year_to', '>=', $car->model_year)
            ->first();

        if (!$version?->weight_kg) return 0;

        $tax = app(DealerFeeRepositoryInterface::class)
            ->findWeightTax((int) $version->weight_kg, $isLight);

        return (int) ($tax?->amount ?? 0);
    }

    /**
     * 自賠責保険料を解決
     */
    public function resolveLiabilityInsurance(object $car, string $vehicleType): int
    {
        $inspectionExpire = $car->detail?->inspection_expire_date;

        if ($inspectionExpire) {
            $months = max(0, (int) now()->diffInMonths($inspectionExpire, false));
        } else {
            $months = InsuranceConstants::DEFAULT_LIABILITY_MONTHS;
        }

        if ($months === 0) return 0;

        $insurance = app(DealerFeeRepositoryInterface::class)
            ->findLiabilityInsurance($vehicleType, $months);

        return (int) ($insurance?->amount ?? 0);
    }

    public function resolveVehicleTax(object $car): int
    {
        $displacement = $car->detail?->displacement;
        if (!$displacement) return 0;

        $isLight = $this->isLightVehicle($car->body_type_id);

        $tax = MstVehicleTax::whereHas('displacementList', function ($q) use ($displacement) {
            $q->where('min_amount', '<=', $displacement)
            ->where(function ($q2) use ($displacement) {
                $q2->where('max_amount', '>=', $displacement)
                    ->orWhere('is_unlimited', 1);
            });
        })->where('is_light', $isLight)->first();

        return (int) ($tax?->amount ?? 0);
    }
}