<?php

namespace App\Domain\Common\Services;

use App\Domain\Common\Repositories\LoanPlanRepositoryInterface;
use Illuminate\Support\Collection;

class LoanPlanResolver
{
    public function __construct(
        private readonly LoanPlanRepositoryInterface $repository,
    ) {}

    /**
     * 単一車両用（CarLoanUseCaseで使用）
     */
    public function resolve(int $carId): ?array
    {
        $carLoan = $this->repository->findCarLoanByCarId($carId);

        // 1. スナップショットあり
        if ($carLoan?->snapshot_rate !== null && $carLoan?->snapshot_months_options) {
            return [
                'rate'           => (float) $carLoan->snapshot_rate,
                'months_options' => $this->decodeMonthsOptions($carLoan->snapshot_months_options),
            ];
        }

        // 2. ディーラーローンプランあり
        if ($carLoan?->dealer_loan_plan_id) {
            $plan = $this->repository->findDealerLoanPlanById($carLoan->dealer_loan_plan_id);
            if ($plan) {
                return [
                    'rate'           => (float) $plan->interest_rate,
                    'months_options' => $this->decodeMonthsOptions($plan->months_options),
                ];
            }
        }

        // 3. デフォルトプラン
        $default = $this->repository->findDefaultLoanPlan();
        if ($default) {
            return [
                'rate'           => (float) $default->interest_rate,
                'months_options' => $this->decodeMonthsOptions($default->months_options),
            ];
        }

        return null;
    }

    /**
     * 一括用（FavoriteCarsUseCase・一覧で使用）
     *
     * @return array ['car_id' => ['rate' => float, 'months_options' => array]|null]
     */
    public function resolveByCarIds(array $carIds): array
    {
        $carLoans    = $this->repository->findCarLoansByCarIds($carIds);
        $defaultPlan = $this->repository->findDefaultLoanPlan();

        // dealer_loan_plan_idを収集して一括取得
        $planIds     = $carLoans->pluck('dealer_loan_plan_id')->filter()->unique()->toArray();
        $dealerPlans = $this->repository->findDealerLoanPlansByIds($planIds);

        $result = [];
        foreach ($carIds as $carId) {
            $result[$carId] = $this->resolveSingle(
                $carLoans[$carId] ?? null,
                $dealerPlans,
                $defaultPlan,
            );
        }

        return $result;
    }

    /**
     * 一括用の内部解決ロジック
     */
    private function resolveSingle(
        ?object $carLoan,
        Collection $dealerPlans,
        ?object $defaultPlan,
    ): ?array {
        // 1. スナップショットあり
        if ($carLoan?->snapshot_rate !== null && $carLoan?->snapshot_months_options) {
            return [
                'rate'           => (float) $carLoan->snapshot_rate,
                'months_options' => $this->decodeMonthsOptions($carLoan->snapshot_months_options),
            ];
        }

        // 2. ディーラーローンプランあり
        if ($carLoan?->dealer_loan_plan_id && isset($dealerPlans[$carLoan->dealer_loan_plan_id])) {
            $plan = $dealerPlans[$carLoan->dealer_loan_plan_id];
            return [
                'rate'           => (float) $plan->interest_rate,
                'months_options' => $this->decodeMonthsOptions($plan->months_options), // ← 修正
            ];
        }

        // 3. デフォルトプラン
        if ($defaultPlan) {
            return [
                'rate'           => (float) $defaultPlan->interest_rate,
                'months_options' => $this->decodeMonthsOptions($defaultPlan->months_options), // ← 修正
            ];
        }

        return null;
    }

    // 共通ヘルパーメソッドを追加
    private function decodeMonthsOptions(mixed $months): array
    {
        if (is_array($months)) {
            return $months;
        }

        // 念のためstring対応（キャストなしモデルが将来追加された場合）
        if (is_string($months)) {
            return json_decode($months, true) ?? [];
        }

        return [];
    }
}