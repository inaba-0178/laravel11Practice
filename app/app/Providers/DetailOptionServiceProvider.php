<?php
 
namespace App\Providers;
 
use App\Domain\DetailOption\Repositories\DetailOptionsRepositoryInterface;
use App\Infrastructure\Repositories\DetailOption\EloquentDetailOptionsRepository;
use Illuminate\Support\ServiceProvider;
 
class DetailOptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            DetailOptionsRepositoryInterface::class,
            EloquentDetailOptionsRepository::class,
        );
    }
}
 