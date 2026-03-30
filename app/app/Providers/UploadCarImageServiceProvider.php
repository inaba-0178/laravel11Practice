<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\UseCases\CarImage\UploadCarImageUseCase;
use App\Domain\CarImage\Repositories\CarImageUploadRepositoryInterface;
use App\Infrastructure\Repositories\CarImage\EloquentCarImageUploadRepository;
use Illuminate\Support\ServiceProvider;

class UploadCarImageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            CarImageUploadRepositoryInterface::class,
            EloquentCarImageUploadRepository::class,
        );

        $this->app->bind(UploadCarImageUseCase::class, function ($app) {
            return new UploadCarImageUseCase(
                $app->make(CarImageUploadRepositoryInterface::class),
            );
        });
    }

    public function boot(): void {}
}