<?php
namespace App\Domain\SelectBodyTypeList\Repositories;

use Illuminate\Support\Collection;

interface CarSeriesBodyTypeRepositoryInterface
{

    public function findBySeriesBodyTypeId(int $bodyTypeId, array $conditions = []): Collection;
}