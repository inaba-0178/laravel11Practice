<?php
 
namespace App\Providers;
 
use App\Domain\CarTypeOption\Repositories\CarTypeOptionRepositoryInterface;
use App\Infrastructure\Repositories\CarTypeOption\EloquentCarTypeOptionRepository;
use Illuminate\Support\ServiceProvider;
 
class CarTypeOptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            CarTypeOptionRepositoryInterface::class,
            EloquentCarTypeOptionRepository::class,
        );
    }
}
 