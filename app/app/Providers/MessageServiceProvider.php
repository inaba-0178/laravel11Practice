<?php
namespace App\Providers;

use App\Domain\Message\Repositories\MessageRepositoryInterface;
use App\Infrastructure\Repositories\Message\MessageRepository;
use App\Infrastructure\Repositories\MessageRead\MessageReadRepository;
use App\Domain\Message\Repositories\MessageReadRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class MessageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(MessageRepositoryInterface::class, MessageRepository::class);
        $this->app->bind(MessageReadRepositoryInterface::class, MessageReadRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}