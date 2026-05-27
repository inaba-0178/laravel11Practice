<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\OprMainViewLists\Repositories\OprMainViewRepositoryInterface;
use App\Infrastructure\Repositories\OprMainViewLists\EloquentOprMainViewRepository;

class OprTopViewListsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            OprMainViewRepositoryInterface::class,
            EloquentOprMainViewRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}