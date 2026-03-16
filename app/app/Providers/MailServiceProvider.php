<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\Services\MailService;
use App\Domain\Mail\Repositories\MailTemplateRepositoryInterface;
use App\Infrastructure\Repositories\Mail\EloquentMailTemplateRepository;
use Illuminate\Support\ServiceProvider;

final class MailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            MailTemplateRepositoryInterface::class,
            EloquentMailTemplateRepository::class,
        );

        $this->app->bind(MailService::class, fn($app) => new MailService(
            $app->make(MailTemplateRepositoryInterface::class)
        ));
    }
}