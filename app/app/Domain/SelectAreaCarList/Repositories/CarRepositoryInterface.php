<?php

namespace App\Domain\SelectAreaCarList\Repositories;

use App\Domain\SelectAreaCarList\Exceptions\SelectAreaCarNotFoundException;

interface CarRepositoryInterface
{
    /**
     * 車両情報を取得
     *
     * @param int    $seriesId
     * @param array  $regionIds
     * @param int    $offset
     * @param int    $limit
     * @param array  $searchParams
     * @param string $sortKey
     * @param string $sortOrder
     * @return array
     * @throws SelectAreaCarNotFoundException
     */
    public function findBySeriesId(int $seriesId, array $regionIds, int $offset, int $limit, array $searchParams = [], string $sortKey = '', string $sortOrder = ''): array;

    public function findByTotalCount(int $seriesId, array $regionIds, array $searchParams = []): int;
}