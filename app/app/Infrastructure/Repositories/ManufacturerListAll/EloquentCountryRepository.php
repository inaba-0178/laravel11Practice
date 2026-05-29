<?php

namespace App\Infrastructure\Repositories\ManufacturerListAll;

use App\Domain\ManufacturerListAll\Repositories\CountryRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstCountries;

class EloquentCountryRepository implements CountryRepositoryInterface
{
    public function findAllActive(): array
    {
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
    }
}