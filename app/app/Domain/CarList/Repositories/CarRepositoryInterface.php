<?php
namespace App\Domain\CarList\Repositories;

use App\Domain\CarList\Exceptions\CarNotFoundException;
use Illuminate\Support\Collection;

interface CarRepositoryInterface
{
    /**
     * 車両情報を取得
     * 
     * @param int $seriesId
     * @return Collection
     * @throws CarNotFoundException
     */
    public function findBySeriesId(int $seriesId): Collection;
}