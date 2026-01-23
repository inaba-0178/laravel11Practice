<?php

namespace App\Providers;

use App\Domain\DisplacementList\Repositories\DisplacementRepositoryInterface;
use App\Infrastructure\Repositories\DisplacementList\EloquentDisplacementListRepository;
use App\Application\UseCases\DisplacementList\DisplacementListUseCase;
use Illuminate\Support\ServiceProvider;

class DisplacementListServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
        // Repository の登録
        $this->app->bind(
            DisplacementRepositoryInterface::class,
            EloquentDisplacementListRepository::class,
        );

        $this->app->bind(DisplacementListUseCase::class, function ($app) {
            return new DisplacementListUseCase(
                $app->make(DisplacementRepositoryInterface::class)
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