<?php

namespace App\Providers;

use App\Domain\Password\Repositories\PasswordResetTokenRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Infrastructure\Repositories\Password\EloquentPasswordResetTokenRepository;
use App\Infrastructure\Repositories\User\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class PasswardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
        // Repository の登録
        $this->app->bind(
            PasswordResetTokenRepositoryInterface::class,
            EloquentPasswordResetTokenRepository::class,
        );

        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class,
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}