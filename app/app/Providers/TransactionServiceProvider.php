<?php

namespace App\Providers;

use App\Domain\Shared\Transactions\UserTransactionInterface;
use App\Infrastructure\Transactions\UserTransaction;
use Illuminate\Support\ServiceProvider;

class TransactionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserTransactionInterface::class,
            UserTransaction::class,
        );
    }
}