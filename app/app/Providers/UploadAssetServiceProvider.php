<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\UseCases\Asset\UploadAssetUseCase;
use App\Domain\Asset\Repositories\AssetUploadRepositoryInterface;
use App\Infrastructure\Repositories\Asset\EloquentAssetUploadRepository;
use Illuminate\Support\ServiceProvider;

class UploadAssetServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AssetUploadRepositoryInterface::class,
            EloquentAssetUploadRepository::class,
        );

        $this->app->bind(UploadAssetUseCase::class, function ($app) {
            return new UploadAssetUseCase(
                $app->make(AssetUploadRepositoryInterface::class),
            );
        });
    }

    public function boot(): void {}
}