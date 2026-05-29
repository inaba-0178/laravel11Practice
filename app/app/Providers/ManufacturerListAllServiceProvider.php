<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\ManufacturerListAll\Repositories\ManufacturerListAllRepositoryInterface;
use App\Domain\ManufacturerListAll\Repositories\CountryRepositoryInterface;
use App\Domain\ManufacturerListAll\Repositories\ManufacturerRepositoryInterface;
use App\Domain\ManufacturerListAll\Repositories\CarCountRepositoryInterface;
use App\Infrastructure\Repositories\ManufacturerListAll\EloquentManufacturerListAllRepository;
use App\Infrastructure\Repositories\ManufacturerListAll\EloquentCountryRepository;
use App\Infrastructure\Repositories\ManufacturerListAll\EloquentManufacturerRepository;
use App\Infrastructure\Repositories\ManufacturerListAll\EloquentCarCountRepository;

class ManufacturerListAllServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ManufacturerListAllRepositoryInterface::class,
            EloquentManufacturerListAllRepository::class,
        );
        $this->app->bind(
            CountryRepositoryInterface::class,
            EloquentCountryRepository::class,
        );
        $this->app->bind(
            ManufacturerRepositoryInterface::class,
            EloquentManufacturerRepository::class,
        );
        $this->app->bind(
            CarCountRepositoryInterface::class,
            EloquentCarCountRepository::class,
        );
    }
}