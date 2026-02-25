<?php
namespace App\Domain\SelectAreaCarList\Repositories;

use App\Domain\SelectAreaCarList\Exceptions\SelectAreaCarNotFoundException;

interface CarRepositoryInterface
{
    /**
     * 車両情報を取得
     * 
     * @param int   $seriesId
     * @param array $regionIds
     * @param int   $offset
     * @param int   $limit
     * @return array
     * @throws SelectAreaCarNotFoundException
     */
    public function findBySeriesId(int $seriesId, array $regionIds, int $offset, int $limit): array;
}