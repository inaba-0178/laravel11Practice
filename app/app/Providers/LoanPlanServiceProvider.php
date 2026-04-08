<?php

namespace App\Providers;

use App\Domain\Common\Repositories\LoanPlanRepositoryInterface;
use App\Infrastructure\Repositories\Common\EloquentLoanPlanRepository;
use Illuminate\Support\ServiceProvider;

class LoanPlanServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            LoanPlanRepositoryInterface::class,
            EloquentLoanPlanRepository::class,
        );
    }
}