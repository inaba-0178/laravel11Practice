<?php
 
namespace App\Providers;
 
use App\Domain\BasicOption\Repositories\BasicOptionsRepositoryInterface;
use App\Infrastructure\Repositories\BasicOption\EloquentBasicOptionsRepository;
use Illuminate\Support\ServiceProvider;
 
class BasicOptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            BasicOptionsRepositoryInterface::class,
            EloquentBasicOptionsRepository::class,
        );
    }
}
 