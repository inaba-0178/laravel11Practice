<?php

namespace App\Providers;

use App\Domain\PriceList\Repositories\PriceRepositoryInterface;
use App\Infrastructure\Repositories\PriceList\EloquentPriceListRepository;
use App\Application\UseCases\PriceList\PriceListUseCase;
use Illuminate\Support\ServiceProvider;

class PriceListServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
        // Repository の登録
        $this->app->bind(
            PriceRepositoryInterface::class,
            EloquentPriceListRepository::class,
        );

        $this->app->bind(PriceListUseCase::class, function ($app) {
            return new PriceListUseCase(
                $app->make(PriceRepositoryInterface::class)
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