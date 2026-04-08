<?php

namespace App\Providers;

use App\Domain\FavoriteCars\Repositories\FavoriteCarsRepositoryInterface;
use App\Infrastructure\Repositories\FavoriteCars\EloquentFavoriteCarsRepository;
use Illuminate\Support\ServiceProvider;

class FavoriteCarsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            FavoriteCarsRepositoryInterface::class,
            EloquentFavoriteCarsRepository::class,
        );
    }
}