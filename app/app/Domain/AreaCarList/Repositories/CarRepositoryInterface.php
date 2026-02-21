<?php
namespace App\Domain\AreaCarList\Repositories;

use App\Domain\AreaCarList\Exceptions\AreaCarNotFoundException;

interface CarRepositoryInterface
{
    /**
     * 車両情報を取得
     * 
     * @param int $seriesId
     * @return array
     * @throws AreaCarNotFoundException
     */
    public function findBySeriesId(int $seriesId): array;
}