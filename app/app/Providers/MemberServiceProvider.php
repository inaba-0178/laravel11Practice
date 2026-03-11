<?php

namespace App\Providers;

use App\Domain\Member\Repositories\MemberRepositoryInterface;
use App\Domain\Member\Repositories\MemberProvisionalRegistrationRepositoryInterface;
use App\Infrastructure\Repositories\Member\EloquentMemberRepository;
use App\Infrastructure\Repositories\Member\EloquentMemberProvisionalRegistrationRepository;
use Illuminate\Support\ServiceProvider;

class MemberServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            MemberRepositoryInterface::class,
            EloquentMemberRepository::class,
        );

        $this->app->bind(
            MemberProvisionalRegistrationRepositoryInterface::class,
            EloquentMemberProvisionalRegistrationRepository::class,
        );
    }

    public function boot(): void
    {
        //
    }
}