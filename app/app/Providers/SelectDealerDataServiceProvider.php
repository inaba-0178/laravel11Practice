<?php

namespace App\Providers;

use App\Domain\SelectDealerData\Repositories\DealerRepositoryInterface;
use App\Infrastructure\Repositories\SelectDealerData\EloquentDealerRepository;
use Illuminate\Support\ServiceProvider;

class SelectDealerDataServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            DealerRepositoryInterface::class,
            EloquentDealerRepository::class,
        );
    }
}