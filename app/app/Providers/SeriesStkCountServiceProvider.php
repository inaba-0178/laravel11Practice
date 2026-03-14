<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\SeriesStkCount\Repositories\SeriesStkCountRepositoryInterface;
use App\Infrastructure\Repositories\SeriesStkCount\EloquentSeriesStkCountRepository;

class SeriesStkCountServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            SeriesStkCountRepositoryInterface::class,
            EloquentSeriesStkCountRepository::class
        );
    }
}