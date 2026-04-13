<?php

namespace App\Providers;

use App\Domain\SelectAreaCarList\Repositories\CarRepositoryInterface;
use App\Infrastructure\Repositories\SelectAreaCarList\EloquentCarRepository;
use App\Domain\Common\Services\TotalPriceCalculator;
use App\Domain\Common\Services\LoanPlanResolver;
use App\Domain\Common\Services\CarMerger;
use Illuminate\Support\ServiceProvider;

class SelectAreaCarServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            CarRepositoryInterface::class,
            EloquentCarRepository::class,
        );

        $this->app->bind(SelectAreaCarListUseCase::class, function ($app) {
            return new SelectAreaCarListUseCase(
                $app->make(CarRepositoryInterface::class),
                $app->make(TotalPriceCalculator::class),
                $app->make(LoanPlanResolver::class),
                $app->make(CarListMapper::class),
                $app->make(CarMerger::class),
            );
        });
    }
}