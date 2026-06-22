<?php

namespace App\Infrastructure\Repositories\ManufacturerListAll;

use App\Domain\ManufacturerListAll\Repositories\CountryRepositoryInterface;
use App\Domain\Common\Constants\CacheConstants;
use App\Infrastructure\Eloquent\Mst\MstCountries;
use Illuminate\Support\Facades\Cache;

class EloquentCountryRepository implements CountryRepositoryInterface
{
    public function findAllActive(): array
    {
        return Cache::remember(CacheConstants::KEY_COUNTRIES, CacheConstants::TTL_MST, function () {
            return MstCountries::query()
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->get()
                ->keyBy('country_code')
                ->map(fn($c) => [
                    'countryCode' => $c->country_code,
                    'label'       => $c->label,
                    'flag'        => $c->flag,
                    'anchor'      => $c->anchor,
                ])
                ->toArray();
        });
    }
}