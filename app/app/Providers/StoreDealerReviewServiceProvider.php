<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\UseCases\StoreDealerReview\StoreDealerReviewUseCase;
use App\Domain\StoreDealerReview\Repositories\StoreDealerReviewRepositoryInterface;
use App\Infrastructure\Repositories\StoreDealerReview\EloquentStoreDealerReviewRepository;
use Illuminate\Support\ServiceProvider;

class StoreDealerReviewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            StoreDealerReviewRepositoryInterface::class,
            EloquentStoreDealerReviewRepository::class,
        );

        $this->app->bind(StoreDealerReviewUseCase::class, function ($app) {
            return new StoreDealerReviewUseCase(
                $app->make(StoreDealerReviewRepositoryInterface::class),
            );
        });
    }

    public function boot(): void {}
}