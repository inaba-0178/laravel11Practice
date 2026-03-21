<?php

namespace App\Infrastructure\Transactions;

use App\Domain\Shared\Transactions\UserTransactionInterface;
use Illuminate\Support\Facades\DB;

class UserTransaction implements UserTransactionInterface
{
    public function run(callable $callback): mixed
    {
        return DB::connection('user')->transaction($callback);
    }
}