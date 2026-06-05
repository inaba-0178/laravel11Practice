<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\UseCases\DealerAnalytics\GetDealerAnalyticsUseCase;

/**
 * 閲覧数記録のDI設定
 */
class DealerAnalyticsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GetDealerAnalyticsUseCase::class, function () {
            return new GetDealerAnalyticsUseCase();
        });
    }

    public function boot(): void {}
}