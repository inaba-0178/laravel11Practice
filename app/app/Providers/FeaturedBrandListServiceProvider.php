<?php

namespace App\Providers;

use App\Domain\FeaturedBrandList\Repositories\FeaturedBrandRepositoryInterface;
use App\Infrastructure\Repositories\FeaturedBrandList\EloquentFeaturedBrandListRepository;
use App\Application\UseCases\FeaturedBrandList\FeaturedBrandListUseCase;
use App\Domain\FeaturedBrandList\Repositories\ManufacturerRepositoryInterface;
use App\Infrastructure\Repositories\FeaturedBrandList\EloquentManufacturersRepository;
use Illuminate\Support\ServiceProvider;

class FeaturedBrandListServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
        // Repository の登録
        $this->app->bind(
            FeaturedBrandRepositoryInterface::class,
            EloquentFeaturedBrandListRepository::class,
        );

        $this->app->bind(
            ManufacturerRepositoryInterface::class,
            EloquentManufacturersRepository::class,
        );

        $this->app->bind(FeaturedBrandListUseCase::class, function ($app) {
            return new FeaturedBrandListUseCase(
                $app->make(FeaturedBrandRepositoryInterface::class),
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