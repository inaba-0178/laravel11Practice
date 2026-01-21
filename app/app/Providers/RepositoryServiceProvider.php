<?php

namespace App\Providers;

use App\Domain\GetRegionList\Repositories\RegionRepositoryInterface;
use App\Infrastructure\Repositories\GetRegionList\EloquentGetRegionListRepository;
use App\Application\UseCases\GetRegionList\GetRegionListUseCase;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
        // Repository の登録
        $this->app->bind(
            RegionRepositoryInterface::class,
            EloquentGetRegionListRepository::class
        );

        // UseCase の登録（シングルトンではなく、リクエストごとに新しいインスタンスを生成）
        $this->app->bind(GetRegionListUseCase::class, function ($app) {
            return new GetRegionListUseCase(
                $app->make(RegionRepositoryInterface::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}