<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Common\Repositories\DealerFeeRepositoryInterface;
use App\Infrastructure\Repositories\Common\EloquentDealerFeeRepository;
use App\Domain\Common\Services\TotalPriceCalculator;
use Illuminate\Support\ServiceProvider;

class DealerFeeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            DealerFeeRepositoryInterface::class,
            EloquentDealerFeeRepository::class,
        );

        $this->app->bind(TotalPriceCalculator::class, function ($app) {
            return new TotalPriceCalculator(
                $app->make(DealerFeeRepositoryInterface::class),
            );
        });
    }
}