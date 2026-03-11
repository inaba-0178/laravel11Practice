<?php

namespace App\Providers;

use App\Domain\MemberAuth\Repositories\MemberAuthRepositoryInterface;
use App\Infrastructure\Repositories\MemberAuth\EloquentMemberAuthRepository;
use Illuminate\Support\ServiceProvider;

class MemberAuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            MemberAuthRepositoryInterface::class,
            EloquentMemberAuthRepository::class,
        );
    }

    public function boot(): void
    {
        //
    }
}