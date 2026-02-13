<?php
namespace App\Domain\SelectBodyTypeList\Repositories;

interface CarSerieRepositoryInterface
{
    /**
     * 特定のボディタイプの車種を取得
     * 
     * @return array
     */
    public function findByCarSeries(array $seriesId, array $conditions = []): array;
}