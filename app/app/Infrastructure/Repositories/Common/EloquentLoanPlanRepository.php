<?php

namespace App\Infrastructure\Repositories\Common;

use App\Domain\Common\Repositories\LoanPlanRepositoryInterface;
use App\Infrastructure\Eloquent\User\StkCarLoan;
use App\Infrastructure\Eloquent\User\StkDealerLoanPlan;
use App\Infrastructure\Eloquent\Mst\MstLoanPlan;
use Illuminate\Support\Collection;

class EloquentLoanPlanRepository implements LoanPlanRepositoryInterface
{
    // 単一用
    public function findCarLoanByCarId(int $carId): ?object
    {
        return StkCarLoan::where('car_id', $carId)->latest()->first();
    }

    public function findDealerLoanPlanById(int $planId): ?object
    {
        return StkDealerLoanPlan::where('id', $planId)
            ->whereNull('deleted_at')
            ->first();
    }

    public function findDefaultLoanPlan(): ?object
    {
        return MstLoanPlan::where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    // 一括用
    public function findCarLoansByCarIds(array $carIds): Collection
    {
        return StkCarLoan::whereIn('car_id', $carIds)
            ->latest()
            ->get()
            ->unique('car_id')  // 最新1件のみ
            ->keyBy('car_id');
    }

    public function findDealerLoanPlansByIds(array $planIds): Collection
    {
        if (empty($planIds)) {
            return collect();
        }

        return StkDealerLoanPlan::whereIn('id', $planIds)
            ->whereNull('deleted_at')
            ->get()
            ->keyBy('id');
    }
}