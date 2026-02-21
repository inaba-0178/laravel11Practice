<?php
namespace App\Domain\AreaCarList\Repositories;

use App\Domain\AreaCarList\Entities\Area;

interface AreaRepositoryInterface
{
    /**
     * すべてのAreaを取得
     * 
     * @return Area[]
     */
    public function findAll(): array;
}