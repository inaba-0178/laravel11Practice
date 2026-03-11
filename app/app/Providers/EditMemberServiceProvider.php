<?php

namespace App\Providers;

use App\Domain\EditMember\Repositories\EditMemberRepositoryInterface;
use App\Infrastructure\Repositories\EditMember\EloquentEditMemberRepository;
use Illuminate\Support\ServiceProvider;

class EditMemberServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            EditMemberRepositoryInterface::class,
            EloquentEditMemberRepository::class,
        );
    }

    public function boot(): void
    {
        //
    }
}