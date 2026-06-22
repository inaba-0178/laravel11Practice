<?php

namespace App\Infrastructure\Repositories\ManufacturerListAll;

use App\Domain\ManufacturerListAll\Repositories\CarCountRepositoryInterface;
use App\Domain\Common\Constants\CacheConstants;
use App\Infrastructure\Eloquent\User\StkCar;
use Illuminate\Support\Facades\Cache;

class EloquentCarCountRepository implements CarCountRepositoryInterface
{
    public function findAvailableCounts(): array
    {
        return Cache::remember(CacheConstants::KEY_CAR_COUNT, CacheConstants::TTL_CAR, function () {
            return StkCar::query()
                ->where('status', 'available')
                ->whereNotNull('manufacturer_id')
                ->groupBy('manufacturer_id')
                ->selectRaw('manufacturer_id, COUNT(*) as count')
                ->pluck('count', 'manufacturer_id')
                ->toArray();
        });
    }
}