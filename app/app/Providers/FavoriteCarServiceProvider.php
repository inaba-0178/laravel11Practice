<?php

namespace App\Providers;

use App\Domain\FavoriteCar\Repositories\FavoriteCarRepositoryInterface;
use App\Infrastructure\Repositories\FavoriteCar\EloquentFavoriteCarRepository;
use Illuminate\Support\ServiceProvider;

class FavoriteCarServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            FavoriteCarRepositoryInterface::class,
            EloquentFavoriteCarRepository::class,
        );
    }
}