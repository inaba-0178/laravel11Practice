<?php
 
namespace App\Providers;
 
use App\Domain\SeatOption\Repositories\SeatOptionRepositoryInterface;
use App\Infrastructure\Repositories\SeatOption\EloquentSeatOptionRepository;
use Illuminate\Support\ServiceProvider;
 
class SeatOptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            SeatOptionRepositoryInterface::class,
            EloquentSeatOptionRepository::class,
        );
    }
}
 