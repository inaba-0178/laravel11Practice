<?php

namespace App\Providers;

use App\Application\UseCases\AreaCarList\AreaCarListUseCase;
use App\Domain\AreaCarList\Repositories\CarRepositoryInterface;
use App\Infrastructure\Repositories\AreaCarList\EloquentCarRepository;
use App\Domain\AreaCarList\Repositories\AreaRepositoryInterface;
use App\Infrastructure\Repositories\AreaCarList\EloquentAreaRepository;
use App\Domain\AreaCarList\Repositories\RegionRepositoryInterface;
use App\Infrastructure\Repositories\AreaCarList\EloquentRegionRepository;
use App\Domain\AreaCarList\Services\AreaCarListDomainService;

use Illuminate\Support\ServiceProvider;

class AreaCarServiceProvider extends ServiceProvider
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
            AreaRepositoryInterface::class,
            EloquentAreaRepository::class,
        );

        $this->app->bind(
            RegionRepositoryInterface::class,
            EloquentRegionRepository::class,
        );

        $this->app->bind(AreaCarListUseCase::class, function ($app) {
            return new AreaCarListUseCase(
                $app->make(CarRepositoryInterface::class),
                $app->make(AreaRepositoryInterface::class),
                $app->make(RegionRepositoryInterface::class),
                $app->make(AreaCarListDomainService::class),
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