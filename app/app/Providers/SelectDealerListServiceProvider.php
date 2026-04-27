<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\UseCases\SelectDealerList\SelectDealerListUseCase;
use App\Domain\SelectDealerList\Repositories\DealerListRepositoryInterface;
use App\Infrastructure\Repositories\SelectDealerList\EloquentDealerListRepository;
use Illuminate\Support\ServiceProvider;

class SelectDealerListServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            DealerListRepositoryInterface::class,
            EloquentDealerListRepository::class,
        );

        $this->app->bind(SelectDealerListUseCase::class, function ($app) {
            return new SelectDealerListUseCase(
                $app->make(DealerListRepositoryInterface::class),
            );
        });
    }

    public function boot(): void {}
}