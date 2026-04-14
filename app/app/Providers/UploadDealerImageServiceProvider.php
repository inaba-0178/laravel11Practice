<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\UseCases\DealerImage\UploadDealerImageUseCase;
use App\Domain\DealerImage\Repositories\DealerImageUploadRepositoryInterface;
use App\Infrastructure\Repositories\DealerImage\EloquentDealerImageUploadRepository;
use Illuminate\Support\ServiceProvider;

class UploadDealerImageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            DealerImageUploadRepositoryInterface::class,
            EloquentDealerImageUploadRepository::class,
        );

        $this->app->bind(UploadDealerImageUseCase::class, function ($app) {
            return new UploadDealerImageUseCase(
                $app->make(DealerImageUploadRepositoryInterface::class),
            );
        });
    }

    public function boot(): void {}
}