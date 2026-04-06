<?php

namespace App\Domain\CarLoan\Repositories;

interface CarLoanRepositoryInterface
{
    /**
     * 車両価格を取得する
     */
    public function findPriceByCarId(int $carId): ?int;

    /**
     * 頭金を取得する
     */
    public function findDownPaymentByCarId(int $carId): ?int;
}