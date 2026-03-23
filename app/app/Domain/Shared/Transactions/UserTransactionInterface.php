<?php

namespace App\Domain\Shared\Transactions;

interface UserTransactionInterface
{
    /**
     * user DBのトランザクション内で処理を実行する
     *
     * @param callable $callback
     * @return mixed
     */
    public function run(callable $callback): mixed;
}