<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\UseCases\Estimate\CreateEstimateUseCase;
use App\Application\Services\EstimatePdfService;
use App\Infrastructure\Repositories\Estimate\EloquentEstimateRepository;
use Illuminate\Support\ServiceProvider;

class EstimateServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CreateEstimateUseCase::class, function () {
            return new CreateEstimateUseCase(
                new EloquentEstimateRepository(),
                app(EstimatePdfService::class),
            );
        });
    }

    public function boot(): void {}
}