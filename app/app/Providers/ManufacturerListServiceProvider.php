<?php

namespace App\Providers;

use App\Application\UseCases\ManufacturerList\ManufacturerListUseCase;
use App\Domain\ManufacturerList\Repositories\ManufacturerRepositoryInterface;
use App\Infrastructure\Repositories\ManufacturerList\EloquentManufacturersRepository;
use Illuminate\Support\ServiceProvider;

class ManufacturerListServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

        $this->app->bind(
            ManufacturerRepositoryInterface::class,
            EloquentManufacturersRepository::class,
        );

        $this->app->bind(ManufacturerListUseCase::class, function ($app) {
            return new ManufacturerListUseCase(
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