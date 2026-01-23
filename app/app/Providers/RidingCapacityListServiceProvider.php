<?php

namespace App\Providers;

use App\Domain\RidingCapacityList\Repositories\RidingCapacityRepositoryInterface;
use App\Infrastructure\Repositories\RidingCapacityList\EloquentRidingCapacityListRepository;
use App\Application\UseCases\RidingCapacityList\RidingCapacityListUseCase;
use Illuminate\Support\ServiceProvider;

class RidingCapacityListServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
        // Repository の登録
        $this->app->bind(
            RidingCapacityRepositoryInterface::class,
            EloquentRidingCapacityListRepository::class,
        );

        $this->app->bind(RidingCapacityListUseCase::class, function ($app) {
            return new RidingCapacityListUseCase(
                $app->make(RidingCapacityRepositoryInterface::class)
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