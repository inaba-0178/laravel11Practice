<?php

namespace App\Providers;

use App\Domain\SelectDealerReview\Repositories\DealerReviewRepositoryInterface;
use App\Infrastructure\Repositories\SelectDealerReview\EloquentDealerReviewRepository;
use Illuminate\Support\ServiceProvider;

class SelectDealerReviewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            DealerReviewRepositoryInterface::class,
            EloquentDealerReviewRepository::class,
        );
    }
}