<?php

namespace App\Providers;

use App\Domain\SelectAreaCarList\Repositories\CarRepositoryInterface;
use App\Infrastructure\Repositories\SelectAreaCarList\EloquentCarRepository;

use Illuminate\Support\ServiceProvider;

class SelectAreaCarServiceProvider extends ServiceProvider
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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}