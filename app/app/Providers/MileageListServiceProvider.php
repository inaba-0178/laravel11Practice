<?php

namespace App\Providers;

use App\Domain\MileageList\Repositories\MileageRepositoryInterface;
use App\Infrastructure\Repositories\MileageList\EloquentMileageListRepository;
use App\Application\UseCases\MileageList\MileageListUseCase;
use Illuminate\Support\ServiceProvider;

class MileageListServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
        // Repository の登録
        $this->app->bind(
            MileageRepositoryInterface::class,
            EloquentMileageListRepository::class,
        );

        $this->app->bind(MileageListUseCase::class, function ($app) {
            return new MileageListUseCase(
                $app->make(MileageRepositoryInterface::class)
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