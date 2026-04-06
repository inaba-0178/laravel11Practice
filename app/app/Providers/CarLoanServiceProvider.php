<?php

namespace App\Providers;

use App\Domain\CarLoan\Repositories\CarLoanRepositoryInterface;
use App\Infrastructure\Repositories\CarLoan\EloquentCarLoanRepository;
use Illuminate\Support\ServiceProvider;

class CarLoanServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            CarLoanRepositoryInterface::class,
            EloquentCarLoanRepository::class,
        );
    }
}