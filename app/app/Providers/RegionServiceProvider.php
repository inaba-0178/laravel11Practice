<?php

namespace App\Providers;

use App\Domain\RegionList\Repositories\RegionRepositoryInterface;
use App\Infrastructure\Repositories\RegionList\EloquentRegionListRepository;
use Illuminate\Support\ServiceProvider;

class RegionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
        // Repository の登録
        $this->app->bind(
            RegionRepositoryInterface::class,
            EloquentRegionListRepository::class
        );

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}