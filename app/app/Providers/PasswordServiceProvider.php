<?php

namespace App\Providers;

use App\Domain\Password\Repositories\PasswordResetTokenRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\MemberPassword\Repositories\MemberPasswordRepositoryInterface;
use App\Infrastructure\Repositories\Password\EloquentPasswordResetTokenRepository;
use App\Infrastructure\Repositories\User\EloquentUserRepository;
use App\Infrastructure\Repositories\MemberPassword\EloquentMemberPasswordRepository;
use Illuminate\Support\ServiceProvider;

class PasswordServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            PasswordResetTokenRepositoryInterface::class,
            EloquentPasswordResetTokenRepository::class,
        );

        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class,
        );

        $this->app->bind(
            MemberPasswordRepositoryInterface::class,
            EloquentMemberPasswordRepository::class,
        );
    }

    public function boot(): void
    {
        //
    }
}