<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\UseCases\CarStock\PublishScheduledCarsUseCase;
use App\Domain\CarStock\Repositories\CarStockRepositoryInterface;
use App\Infrastructure\Repositories\CarStock\EloquentCarStockRepository;
use Illuminate\Support\ServiceProvider;

class CarStockServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CarStockRepositoryInterface::class, function () {
            return new EloquentCarStockRepository();
        });

        $this->app->bind(PublishScheduledCarsUseCase::class, function () {
            return new PublishScheduledCarsUseCase(
                new EloquentCarStockRepository(),
            );
        });
    }

    public function boot(): void {}
}