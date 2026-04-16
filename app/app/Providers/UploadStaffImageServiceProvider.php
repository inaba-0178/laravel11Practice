<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\UseCases\StaffImage\UploadStaffImageUseCase;
use App\Domain\StaffImage\Repositories\StaffImageUploadRepositoryInterface;
use App\Infrastructure\Repositories\StaffImage\EloquentStaffImageUploadRepository;
use Illuminate\Support\ServiceProvider;

class UploadStaffImageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            StaffImageUploadRepositoryInterface::class,
            EloquentStaffImageUploadRepository::class,
        );

        $this->app->bind(UploadStaffImageUseCase::class, function ($app) {
            return new UploadStaffImageUseCase(
                $app->make(StaffImageUploadRepositoryInterface::class),
            );
        });
    }

    public function boot(): void {}
}