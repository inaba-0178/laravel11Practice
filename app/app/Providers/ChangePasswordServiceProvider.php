<?php

namespace App\Providers;

use App\Domain\ChangePassword\Repositories\ChangePasswordRepositoryInterface;
use App\Infrastructure\Repositories\ChangePassword\EloquentChangePasswordRepository;
use Illuminate\Support\ServiceProvider;

class ChangePasswordServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ChangePasswordRepositoryInterface::class,
            EloquentChangePasswordRepository::class,
        );
    }
}