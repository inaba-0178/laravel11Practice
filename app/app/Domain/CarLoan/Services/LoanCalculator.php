<?php

namespace App\Domain\CarLoan\Services;

use App\Domain\Common\Constants\LoanConstants;

class LoanCalculator
{
    /**
     * 元利均等返済で月額を計算
     *
     * @param  float $principal  借入元本（円）
     * @param  float $annualRate 実質年率（%）
     * @param  int   $months     返済回数
     * @return int   月額（円・切り上げ）
     */
    public static function monthlyPayment(float $principal, float $annualRate, int $months): int
    {
        if ($annualRate <= 0 || $months <= 0) {
            return 0;
        }

        $monthlyRate = $annualRate / LoanConstants::ANNUAL_INTEREST_RATE / LoanConstants::MONTHLY_INTEREST;

        $amount = $principal * $monthlyRate * pow(1 + $monthlyRate, $months)
                / (pow(1 + $monthlyRate, $months) - 1);

        return (int) ceil($amount);
    }

    /**
     * 割賦販売価格（総支払額）
     *
     * @param  int   $monthly     月額
     * @param  int   $months      返済回数
     * @param  float $downPayment 頭金
     * @return int   総支払額（円）
     */
    public static function totalPayment(int $monthly, int $months, float $downPayment = 0): int
    {
        return (int) ($monthly * $months + $downPayment);
    }
}