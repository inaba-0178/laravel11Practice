<?php

declare(strict_types=1);

namespace App\Domain\Common\Services;

use App\Domain\CarLoan\Services\LoanCalculator;
use Illuminate\Support\Collection;

class CarListMapper
{
    /**
     * ローン月額を解決
     */
    public function resolveLoanMonthly(float $price, array $loanPlans, int $carId): ?int
    {
        $plan   = $loanPlans[$carId] ?? null;
        $months = $plan ? collect($plan['months_options'])->last() : null;

        if (!$plan || !$months) return null;

        return LoanCalculator::monthlyPayment($price, $plan['rate'], $months);
    }
}