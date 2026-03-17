<?php
 
namespace App\Providers;
 
use App\Domain\ColorOption\Repositories\ColorOptionsRepositoryInterface;
use App\Infrastructure\Repositories\ColorOption\EloquentColorOptionsRepository;
use Illuminate\Support\ServiceProvider;
 
class ColorOptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ColorOptionsRepositoryInterface::class,
            EloquentColorOptionsRepository::class,
        );
    }
}
 