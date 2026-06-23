<?php

namespace App\Infrastructure\Repositories\EquipmentDressup;

use App\Domain\EquipmentDressup\Repositories\EquipmentDressupRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstEquipmentDressup;
use App\Domain\EquipmentDressup\Entities\EquipmentDressup;
use App\Domain\Common\Constants\CacheConstants;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EloquentEquipmentDressupRepository implements EquipmentDressupRepositoryInterface
{
    public function getAll(): Collection
    {
        return Cache::remember(CacheConstants::KEY_EQUIPMENT_DRESSUP, CacheConstants::TTL_MST, function () {
            return MstEquipmentDressup::where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn($model) => new EquipmentDressup(
                    id:        $model->id,
                    value:     $model->value,
                    label:     $model->label,
                    sortOrder: $model->sort_order,
                    isActive:  $model->is_active,
                ));
        });
    }
}
