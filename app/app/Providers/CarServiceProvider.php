<?php

namespace App\Providers;

use App\Application\UseCases\CarList\CarListUseCase;
use App\Domain\CarList\Repositories\CarRepositoryInterface;
use App\Infrastructure\Repositories\CarList\EloquentCarRepository;
use App\Domain\CarList\Repositories\CarDetailRepositoryInterface;
use App\Infrastructure\Repositories\CarList\EloquentCarDetailRepository;

use Illuminate\Support\ServiceProvider;

class CarServiceProvider extends ServiceProvider
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
        $this->app->bind(CarListUseCase::class, function ($app) {
            return new CarListUseCase(
                $app->make(CarRepositoryInterface::class),
                $app->make(CarDetailRepositoryInterface::class),
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