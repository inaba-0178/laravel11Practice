<?php

namespace App\Providers;

use App\Domain\SelectManufacturerList\Repositories\CarSerieRepositoryInterface;
use App\Infrastructure\Repositories\SelectManufacturerList\EloquentCarSeriesRepository;
use App\Application\UseCases\SelectManufacturerList\SelectManufacturerListUseCase;
use App\Domain\SelectManufacturerList\Repositories\ManufacturerRepositoryInterface;
use App\Infrastructure\Repositories\SelectManufacturerList\EloquentManufacturersRepository;
use Illuminate\Support\ServiceProvider;

class SelectManufacturerListServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
        // Repository の登録
        $this->app->bind(
            CarSerieRepositoryInterface::class,
            EloquentCarSeriesRepository::class,
        );
        
        $this->app->bind(
            ManufacturerRepositoryInterface::class,
            EloquentManufacturersRepository::class,
        );

        $this->app->bind(SelectManufacturerListUseCase::class, function ($app) {
            return new SelectManufacturerListUseCase(
                $app->make(CarSerieRepositoryInterface::class),
                $app->make(ManufacturerRepositoryInterface::class),
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