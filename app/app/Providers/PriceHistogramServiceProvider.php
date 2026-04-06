<?php

namespace App\Providers;

use App\Domain\PriceHistogram\Repositories\PriceHistogramRepositoryInterface;
use App\Infrastructure\Repositories\PriceHistogram\EloquentPriceHistogramRepository;
use Illuminate\Support\ServiceProvider;

class PriceHistogramServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            PriceHistogramRepositoryInterface::class,
            EloquentPriceHistogramRepository::class,
        );
    }
}