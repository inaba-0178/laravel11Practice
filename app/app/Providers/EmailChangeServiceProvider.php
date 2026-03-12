<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\UseCases\EmailChange\UpdateEmailUseCase;
use App\Domain\EmailChange\Repositories\EmailChangeRepositoryInterface;
use App\Infrastructure\Repositories\EmailChange\EloquentEmailChangeRepository;
use Illuminate\Support\ServiceProvider;

final class EmailChangeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EmailChangeRepositoryInterface::class, EloquentEmailChangeRepository::class);

        $this->app->bind(UpdateEmailUseCase::class, fn($app) => new UpdateEmailUseCase(
            $app->make(EmailChangeRepositoryInterface::class)
        ));
    }
}