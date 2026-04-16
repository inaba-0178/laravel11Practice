<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\UseCases\SelectDealerContentData\SelectDealerContentDataUseCase;
use App\Domain\SelectDealerContentData\Repositories\DealerContentRepositoryInterface;
use App\Infrastructure\Repositories\SelectDealerContentData\EloquentDealerContentRepository;
use Illuminate\Support\ServiceProvider;

class SelectDealerContentDataServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            DealerContentRepositoryInterface::class,
            EloquentDealerContentRepository::class,
        );

        $this->app->bind(SelectDealerContentDataUseCase::class, function ($app) {
            return new SelectDealerContentDataUseCase(
                $app->make(DealerContentRepositoryInterface::class),
            );
        });
    }

    public function boot(): void {}
}