<?php

namespace App\Application\UseCases\CarLoan;

use App\Domain\CarLoan\Services\LoanCalculator;
use App\Domain\CarLoan\Services\LoanPlanResolver;
use App\Domain\CarLoan\ValueObjects\CarId;
use App\Domain\CarLoan\Repositories\CarLoanRepositoryInterface;

class CarLoanUseCase
{
    public function __construct(
        private readonly LoanPlanResolver           $loanPlanResolver,
        private readonly CarLoanRepositoryInterface $carLoanRepository,
    ) {}

    /**
     * 車両のローン情報を取得する
     *
     * @return CarLoanOutputData
     */
    public function execute(CarId $carId): CarLoanOutputData
    {
        $plan = $this->loanPlanResolver->resolve($carId->getValue());

        // 取得できない場合はnull（Vue側で非表示）
        if ($plan === null) {
            return new CarLoanOutputData(null);
        }

        $price       = $this->carLoanRepository->findPriceByCarId($carId->getValue());
        $downPayment = $this->carLoanRepository->findDownPaymentByCarId($carId->getValue()) ?? 0;

        // 車両価格が取得できない場合はnull（Vue側で非表示）
        if ($price === null) {
            return new CarLoanOutputData(null);
        }

        $principal = $price - $downPayment;

        $payments = [];
        foreach ($plan['months_options'] as $months) {
            $payments[] = [
                'months' => $months,
                'amount' => LoanCalculator::monthlyPayment($principal, $plan['rate'], $months),
            ];
        }

        return new CarLoanOutputData([
            'interest_rate'    => $plan['rate'],
            'down_payment'     => $downPayment,
            'monthly_payments' => $payments,
        ]);
    }
}