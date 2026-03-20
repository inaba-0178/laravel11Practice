<?php

namespace App\Providers;

use App\Domain\SelectVehicleSpec\Repositories\VehicleRepositoryInterface;
use App\Domain\SelectVehicleSpec\Repositories\VehicleVersionRepositoryInterface;
use App\Infrastructure\Repositories\SelectVehicleSpec\EloquentVehicleRepository;
use App\Infrastructure\Repositories\SelectVehicleSpec\EloquentVehicleVersionRepository;
use Illuminate\Support\ServiceProvider;

class SelectVehicleSpecServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            VehicleRepositoryInterface::class,
            EloquentVehicleRepository::class,
        );

        $this->app->bind(
            VehicleVersionRepositoryInterface::class,
            EloquentVehicleVersionRepository::class,
        );
    }
}