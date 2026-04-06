<?php

namespace App\Infrastructure\Repositories\CarLoan;

use App\Domain\CarLoan\Repositories\CarLoanRepositoryInterface;
use App\Infrastructure\Eloquent\User\StkCar;
use App\Infrastructure\Eloquent\User\StkCarLoan;

class EloquentCarLoanRepository implements CarLoanRepositoryInterface
{
    /**
     * 車両価格を取得する
     */
    public function findPriceByCarId(int $carId): ?int
    {
        $car = StkCar::find($carId);

        return $car ? (int) $car->price : null;
    }

    /**
     * 頭金を取得する
     */
    public function findDownPaymentByCarId(int $carId): ?int
    {
        $carLoan = StkCarLoan::where('car_id', $carId)->latest()->first();

        return $carLoan ? (int) $carLoan->down_payment : null;
    }
}