<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\SelectConditionCarList\Repositories\CarRepositoryInterface;
use App\Infrastructure\Repositories\SelectConditionCarList\EloquentCarRepository;
use App\Application\UseCases\SelectConditionCarList\SelectConditionCarListUseCase;
use App\Domain\Common\Services\TotalPriceCalculator;
use App\Domain\Common\Services\LoanPlanResolver;
use App\Domain\Common\Services\CarMerger;
use Illuminate\Support\ServiceProvider;

class SelectConditionCarListServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            CarRepositoryInterface::class,
            EloquentCarRepository::class,
        );

        $this->app->bind(SelectConditionCarListUseCase::class, function ($app) {
            return new SelectConditionCarListUseCase(
                $app->make(CarRepositoryInterface::class),
                $app->make(TotalPriceCalculator::class),
                $app->make(LoanPlanResolver::class),
                $app->make(CarMerger::class),
            );
        });
    }

    public function boot(): void {}
}