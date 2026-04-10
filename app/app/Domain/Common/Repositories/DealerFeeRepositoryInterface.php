<?php

declare(strict_types=1);

namespace App\Domain\Common\Repositories;

use Illuminate\Support\Collection;

interface DealerFeeRepositoryInterface
{
    /**
     * car_idの配列でディーラー諸費用を一括取得
     */
    public function findByCarIds(array $carIds): Collection;

    /**
     * 単一car_idでディーラー諸費用を取得
     */
    public function findByCarId(int $carId): ?object;

    /**
     * 重量からweight_taxを取得
     */
    public function findWeightTax(int $weightKg, bool $isLight): ?object;

    /**
     * 車種区分・期間で自賠責保険料を取得
     */
    public function findLiabilityInsurance(string $vehicleType, int $months): ?object;
}