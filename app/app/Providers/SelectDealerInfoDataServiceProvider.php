<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\UseCases\SelectDealerInfoData\SelectDealerInfoDataUseCase;
use App\Domain\SelectDealerInfoData\Repositories\DealerRepositoryInterface;
use App\Domain\SelectDealerInfoData\Repositories\DealerImageRepositoryInterface;
use App\Infrastructure\Repositories\SelectDealerInfoData\EloquentDealerRepository;
use App\Infrastructure\Repositories\SelectDealerInfoData\EloquentDealerImageRepository;
use Illuminate\Support\ServiceProvider;

class SelectDealerInfoDataServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            DealerRepositoryInterface::class,
            EloquentDealerRepository::class,
        );

        $this->app->bind(
            DealerImageRepositoryInterface::class,
            EloquentDealerImageRepository::class,
        );

        $this->app->bind(SelectDealerInfoDataUseCase::class, function ($app) {
            return new SelectDealerInfoDataUseCase(
                $app->make(DealerRepositoryInterface::class),
                $app->make(DealerImageRepositoryInterface::class),
            );
        });
    }

    public function boot(): void {}
}