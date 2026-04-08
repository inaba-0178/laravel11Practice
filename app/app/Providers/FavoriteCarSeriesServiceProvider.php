<?php

namespace App\Providers;

use App\Domain\FavoriteCars\Repositories\FavoriteCarSeriesRepositoryInterface;
use App\Infrastructure\Repositories\FavoriteCars\EloquentFavoriteCarSeriesRepository;
use Illuminate\Support\ServiceProvider;

class FavoriteCarSeriesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            FavoriteCarSeriesRepositoryInterface::class,
            EloquentFavoriteCarSeriesRepository::class,
        );
    }
}