<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\UseCases\SelectDealerStaffData\SelectDealerStaffDataUseCase;
use App\Domain\SelectDealerStaffData\Repositories\DealerStaffRepositoryInterface;
use App\Infrastructure\Repositories\SelectDealerStaffData\EloquentDealerStaffRepository;
use Illuminate\Support\ServiceProvider;

class SelectDealerStaffDataServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            DealerStaffRepositoryInterface::class,
            EloquentDealerStaffRepository::class,
        );

        $this->app->bind(SelectDealerStaffDataUseCase::class, function ($app) {
            return new SelectDealerStaffDataUseCase(
                $app->make(DealerStaffRepositoryInterface::class),
            );
        });
    }

    public function boot(): void {}
}