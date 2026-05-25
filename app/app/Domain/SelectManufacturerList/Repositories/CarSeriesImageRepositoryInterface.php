<?php
namespace App\Domain\SelectManufacturerList\Repositories;

interface CarSeriesImageRepositoryInterface
{
    /**
     * 指定されたシリーズIDに紐づく画像を取得
     *
     * @param int[] $seriesIds
     * @return CarSeriesImage[]
     */
    public function findBySeriesIds(array $seriesIds, array $conditions = []): array;
}