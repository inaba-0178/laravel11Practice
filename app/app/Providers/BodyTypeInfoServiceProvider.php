<?php

namespace App\Providers;

use App\Application\UseCases\BodyTypeInfo\BodyTypeInfoUseCase;
use App\Domain\BodyTypeInfo\Repositories\BodyTypeRepositoryInterface;
use App\Infrastructure\Repositories\BodyTypeInfo\EloquentBodyTypeRepository;
use Illuminate\Support\ServiceProvider;

class BodyTypeInfoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
        // Repository の登録
        $this->app->bind(
            BodyTypeRepositoryInterface::class,
            EloquentBodyTypeRepository::class,
        );

        $this->app->bind(BodyTypeInfoUseCase::class, function ($app) {
            return new BodyTypeInfoUseCase(
                $app->make(BodyTypeRepositoryInterface::class),
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