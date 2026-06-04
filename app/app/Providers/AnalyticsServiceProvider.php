<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\UseCases\Analytics\RecordViewCountUseCase;
use App\Infrastructure\Repositories\Analytics\EloquentAnalyticsRepository;
use Illuminate\Support\ServiceProvider;

/**
 * 閲覧数記録のDI設定
 */
class AnalyticsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RecordViewCountUseCase::class, function () {
            return new RecordViewCountUseCase(
                new EloquentAnalyticsRepository(),
            );
        });
    }

    public function boot(): void {}
}