<?php

namespace App\Providers;

use App\Domain\SelectBodyTypeList\Repositories\CarSeriesBodyTypeRepositoryInterface;
use App\Infrastructure\Repositories\SelectBodyTypeList\EloquentCarSeriesBodyTypesRepository;
use App\Domain\SelectBodyTypeList\Repositories\CarSerieRepositoryInterface;
use App\Infrastructure\Repositories\SelectBodyTypeList\EloquentCarSeriesRepository;
use App\Application\UseCases\SelectBodyTypeList\SelectBodyTypeListUseCase;
use App\Domain\SelectBodyTypeList\Repositories\BodyTypeRepositoryInterface;
use App\Infrastructure\Repositories\SelectBodyTypeList\EloquentBodyTypeRepository;
use App\Domain\Common\Services\JapaneseInitialGroupingService;
use Illuminate\Support\ServiceProvider;

class SelectBodyTypeListServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
        // Repository の登録
        $this->app->bind(
            CarSeriesBodyTypeRepositoryInterface::class,
            EloquentCarSeriesBodyTypesRepository::class,
        );

        $this->app->bind(
            CarSerieRepositoryInterface::class,
            EloquentCarSeriesRepository::class,
        );
        
        $this->app->bind(
            BodyTypeRepositoryInterface::class,
            EloquentBodyTypeRepository::class,
        );

        $this->app->singleton(JapaneseInitialGroupingService::class, function ($app) {
            return new JapaneseInitialGroupingService();
        });

        $this->app->bind(SelectBodyTypeListUseCase::class, function ($app) {
            return new SelectBodyTypeListUseCase(
                $app->make(CarSeriesBodyTypeRepositoryInterface::class),
                $app->make(CarSerieRepositoryInterface::class),
                $app->make(BodyTypeRepositoryInterface::class),
                //$app->make(JapaneseInitialGroupingService::class),
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