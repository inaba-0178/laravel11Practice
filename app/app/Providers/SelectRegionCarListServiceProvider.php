<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\SelectRegionCarList\Repositories\CarRepositoryInterface;
use App\Infrastructure\Repositories\SelectRegionCarList\EloquentCarRepository;
use App\Application\UseCases\SelectRegionCarList\SelectRegionCarListUseCase;
use App\Domain\Common\Services\TotalPriceCalculator;
use App\Domain\Common\Services\LoanPlanResolver;
use App\Domain\Common\Services\CarMerger;
use Illuminate\Support\ServiceProvider;

class SelectRegionCarListServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            CarRepositoryInterface::class,
            EloquentCarRepository::class,
        );

        $this->app->bind(SelectRegionCarListUseCase::class, function ($app) {
            return new SelectRegionCarListUseCase(
                $app->make(CarRepositoryInterface::class),
                $app->make(TotalPriceCalculator::class),
                $app->make(LoanPlanResolver::class),
                $app->make(CarMerger::class),
            );
        });
    }
}