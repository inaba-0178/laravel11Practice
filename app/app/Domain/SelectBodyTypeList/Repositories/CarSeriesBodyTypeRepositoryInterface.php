<?php
namespace App\Domain\SelectBodyTypeList\Repositories;

use Illuminate\Support\Collection;

interface CarSeriesBodyTypeRepositoryInterface
{
    /**
     * すべてのCarSerieを取得
     * 
     * @return CarSerie[]
     */
    public function findAll(): array;

    public function findBySeriesBodyTypeId(int $BodyTypeId, array $conditions = []): Collection;
}