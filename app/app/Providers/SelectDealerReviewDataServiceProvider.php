<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\UseCases\SelectDealerReviewData\SelectDealerReviewDataUseCase;
use App\Domain\SelectDealerReviewData\Repositories\DealerReviewRepositoryInterface;
use App\Infrastructure\Repositories\SelectDealerReviewData\EloquentDealerReviewRepository;
use Illuminate\Support\ServiceProvider;

class SelectDealerReviewDataServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            DealerReviewRepositoryInterface::class,
            EloquentDealerReviewRepository::class,
        );

        $this->app->bind(SelectDealerReviewDataUseCase::class, function ($app) {
            return new SelectDealerReviewDataUseCase(
                $app->make(DealerReviewRepositoryInterface::class),
            );
        });
    }

    public function boot(): void {}
}