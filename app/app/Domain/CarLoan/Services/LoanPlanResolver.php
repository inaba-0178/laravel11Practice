<?php

namespace App\Domain\CarLoan\Services;

use App\Infrastructure\Eloquent\User\StkCarLoan;
use App\Infrastructure\Eloquent\User\StkDealerLoanPlan;
use App\Infrastructure\Eloquent\Mst\MstLoanPlan;

class LoanPlanResolver
{
    /**
     * 優先順位に従いローンパラメータを解決する
     *
     * @param  int $carId
     * @return array{rate: float, months_options: array}|null
     */
    public function resolve(int $carId): ?array
    {
        $carLoan = StkCarLoan::where('car_id', $carId)->latest()->first();

        // 1. スナップショットあり
        if ($carLoan?->snapshot_rate !== null) {
            $months = $carLoan->snapshot_months_options;
            if ($months) {
                return [
                    'rate'          => (float) $carLoan->snapshot_rate,
                    'months_options' => $months,
                ];
            }
        }

        // 2. ディーラーローンプランあり
        if ($carLoan?->dealer_loan_plan_id) {
            $plan = StkDealerLoanPlan::where('id', $carLoan->dealer_loan_plan_id)
                        ->whereNull('deleted_at')
                        ->first();
            if ($plan) {
                return [
                    'rate'           => (float) $plan->interest_rate,
                    'months_options' => json_decode($plan->months_options, true),
                ];
            }
        }

        // 3. システムデフォルトプラン
        $default = MstLoanPlan::where('is_default', true)
                      ->where('is_active', true)
                      ->first();
        if ($default) {
            return [
                'rate'           => (float) $default->interest_rate,
                'months_options' => json_decode($default->months_options, true),
            ];
        }

        // 4. 取得できない場合はnull（Vue側で非表示）
        return null;
    }
}