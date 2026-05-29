<?php

namespace App\Infrastructure\Repositories\ManufacturerListAll;

use App\Domain\ManufacturerListAll\Repositories\ManufacturerRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstManufacturers;

class EloquentManufacturerRepository implements ManufacturerRepositoryInterface
{
    public function findAllActive(): array
    {
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
    }
}