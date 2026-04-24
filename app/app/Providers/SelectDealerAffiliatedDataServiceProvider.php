<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\UseCases\SelectDealerAffiliatedData\SelectDealerAffiliatedDataUseCase;
use App\Domain\SelectDealerAffiliatedData\Repositories\AffiliatedStoreRepositoryInterface;
use App\Infrastructure\Repositories\SelectDealerAffiliatedData\EloquentAffiliatedStoreRepository;
use Illuminate\Support\ServiceProvider;

class SelectDealerAffiliatedDataServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AffiliatedStoreRepositoryInterface::class,
            EloquentAffiliatedStoreRepository::class,
        );

        $this->app->bind(SelectDealerAffiliatedDataUseCase::class, function ($app) {
            return new SelectDealerAffiliatedDataUseCase(
                $app->make(AffiliatedStoreRepositoryInterface::class),
            );
        });
    }

    public function boot(): void {}
}