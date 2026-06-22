<?php

namespace App\Infrastructure\Repositories\ManufacturerListAll;

use App\Domain\ManufacturerListAll\Repositories\ManufacturerRepositoryInterface;
use App\Domain\Common\Constants\CacheConstants;
use App\Infrastructure\Eloquent\Mst\MstManufacturers;
use Illuminate\Support\Facades\Cache;

class EloquentManufacturerRepository implements ManufacturerRepositoryInterface
{
    public function findAllActive(): array
    {
        return Cache::remember(CacheConstants::KEY_MANUFACTURERS_ACTIVE, CacheConstants::TTL_MST, function () {
            return MstManufacturers::query()
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->get()
                ->map(fn($m) => [
                    'id'          => $m->id,
                    'name'        => $m->name,
                    'displayName' => $m->display_name,
                    'code'        => $m->code,
                    'countryCode' => $m->country_code,
                ])
                ->toArray();
        });
    }
}