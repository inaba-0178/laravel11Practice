<?php

namespace App\Infrastructure\Repositories\ManufacturerListAll;

use App\Domain\ManufacturerListAll\Repositories\CarCountRepositoryInterface;
use App\Infrastructure\Eloquent\User\StkCar;

class EloquentCarCountRepository implements CarCountRepositoryInterface
{
    public function findAvailableCounts(): array
    {
        return StkCar::query()
            ->where('status', 'available')
            ->whereNotNull('manufacturer_id')
            ->groupBy('manufacturer_id')
            ->selectRaw('manufacturer_id, COUNT(*) as count')
            ->pluck('count', 'manufacturer_id')
            ->toArray();
    }
}