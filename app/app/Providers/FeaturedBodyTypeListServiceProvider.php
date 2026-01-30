<?php

namespace App\Providers;

use App\Domain\FeaturedBodyTypeList\Repositories\FeaturedBodyTypeRepositoryInterface;
use App\Infrastructure\Repositories\FeaturedBodyTypeList\EloquentFeaturedBodyTypeListRepository;
use App\Application\UseCases\FeaturedBodyTypeList\FeaturedBodyTypeListUseCase;
use App\Domain\FeaturedBodyTypeList\Repositories\BodyTypeRepositoryInterface;
use App\Infrastructure\Repositories\FeaturedBodyTypeList\EloquentBodyTypesRepository;
use App\Domain\FeaturedBodyTypeList\Repositories\BodyTypeImageRepositoryInterface;
use App\Infrastructure\Repositories\FeaturedBodyTypeList\EloquentBodyTypeImagesRepository;
use App\Domain\FeaturedBodyTypeList\Services\BodyTypeInfoFactory;
use Illuminate\Support\ServiceProvider;

class FeaturedBodyTypeListServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
        // Repository の登録
        $this->app->bind(
            FeaturedBodyTypeRepositoryInterface::class,
            EloquentFeaturedBodyTypeListRepository::class,
        );

        $this->app->bind(
            BodyTypeRepositoryInterface::class,
            EloquentBodyTypesRepository::class,
        );

        $this->app->bind(
            BodyTypeImageRepositoryInterface::class,
            EloquentBodyTypeImagesRepository::class,
        );

        $this->app->bind(BodyTypeInfoFactory::class, function ($app) {
            return new BodyTypeInfoFactory();
        });

        $this->app->bind(FeaturedBodyTypeListUseCase::class, function ($app) {
            return new FeaturedBodyTypeListUseCase(
                $app->make(FeaturedBodyTypeRepositoryInterface::class),
                $app->make(BodyTypeRepositoryInterface::class),
                $app->make(BodyTypeImageRepositoryInterface::class),
                $app->make(BodyTypeInfoFactory::class),
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