<?php

namespace App\Providers;

use App\Application\UseCases\SelectCarData\SelectCarDataUseCase;
use App\Domain\SelectCarData\Repositories\CarRepositoryInterface;
use App\Infrastructure\Repositories\SelectCarData\EloquentCarRepository;
use App\Domain\SelectCarData\Repositories\CarDetailRepositoryInterface;
use App\Infrastructure\Repositories\SelectCarData\EloquentCarDetailRepository;
use App\Domain\SelectCarData\Repositories\CarImageRepositoryInterface;
use App\Infrastructure\Repositories\SelectCarData\EloquentCarImageRepository;
use App\Domain\SelectCarData\Repositories\CarOptionRepositoryInterface;
use App\Infrastructure\Repositories\SelectCarData\EloquentCarOptionRepository;
use App\Domain\Common\Services\TotalPriceCalculator;
use Illuminate\Support\ServiceProvider;

class SelectCarDataServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
        // Repository の登録
        $this->app->bind(
            CarRepositoryInterface::class,
            EloquentCarRepository::class,
        );

        $this->app->bind(
            CarDetailRepositoryInterface::class,
            EloquentCarDetailRepository::class,
        );

        $this->app->bind(
            CarImageRepositoryInterface::class,
            EloquentCarImageRepository::class,
        );

        $this->app->bind(
            CarOptionRepositoryInterface::class,
            EloquentCarOptionRepository::class,
        );

        $this->app->bind(SelectCarDataUseCase::class, function ($app) {
            return new SelectCarDataUseCase(
                $app->make(CarRepositoryInterface::class),
                $app->make(CarDetailRepositoryInterface::class),
                $app->make(CarImageRepositoryInterface::class),
                $app->make(CarOptionRepositoryInterface::class),
                $app->make(TotalPriceCalculator::class),
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}