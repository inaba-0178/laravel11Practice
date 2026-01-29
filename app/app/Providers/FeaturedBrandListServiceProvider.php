<?php

namespace App\Providers;

use App\Domain\FeaturedBrandList\Repositories\FeaturedBrandRepositoryInterface;
use App\Infrastructure\Repositories\FeaturedBrandList\EloquentFeaturedBrandListRepository;
use App\Application\UseCases\FeaturedBrandList\FeaturedBrandListUseCase;
use App\Domain\FeaturedBrandList\Repositories\ManufacturerRepositoryInterface;
use App\Infrastructure\Repositories\FeaturedBrandList\EloquentManufacturersRepository;
use App\Domain\FeaturedBrandList\Repositories\ManufacturerImageRepositoryInterface;
use App\Infrastructure\Repositories\FeaturedBrandList\EloquentManufacturerImagesRepository;
use App\Domain\FeaturedBrandList\Services\ManufacturerInfoFactory;
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

        $this->app->bind(
            ManufacturerImageRepositoryInterface::class,
            EloquentManufacturerImagesRepository::class,
        );

        $this->app->bind(ManufacturerInfoFactory::class, function ($app) {
            return new ManufacturerInfoFactory();
        });

        $this->app->bind(FeaturedBrandListUseCase::class, function ($app) {
            return new FeaturedBrandListUseCase(
                $app->make(FeaturedBrandRepositoryInterface::class),
                $app->make(ManufacturerRepositoryInterface::class),
                $app->make(ManufacturerImageRepositoryInterface::class),
                $app->make(ManufacturerInfoFactory::class),
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