<?php
namespace App\Providers;

use App\Domain\Room\Repositories\RoomRepositoryInterface;
use App\Infrastructure\Repositories\Room\RoomRepository;
use Illuminate\Support\ServiceProvider;

class RoomServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
        $this->app->bind(RoomRepositoryInterface::class, RoomRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}