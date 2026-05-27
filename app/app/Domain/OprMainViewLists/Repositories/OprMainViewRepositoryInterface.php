<?php

namespace App\Domain\OprMainViewLists\Repositories;

use App\Domain\OprMainViewLists\Entities\OprMainView;

interface OprMainViewRepositoryInterface
{
    /**
     * 有効なスライドを取得
     * @return OprMainView[]
     */
    public function findActive(): array;
}