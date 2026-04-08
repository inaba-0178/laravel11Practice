<?php

namespace App\Domain\Common\Repositories;

use Illuminate\Support\Collection;

interface LoanPlanRepositoryInterface
{
    // 単一用
    public function findCarLoanByCarId(int $carId): ?object;
    public function findDealerLoanPlanById(int $planId): ?object;
    public function findDefaultLoanPlan(): ?object;

    // 一括用
    public function findCarLoansByCarIds(array $carIds): Collection;
    public function findDealerLoanPlansByIds(array $planIds): Collection;
}