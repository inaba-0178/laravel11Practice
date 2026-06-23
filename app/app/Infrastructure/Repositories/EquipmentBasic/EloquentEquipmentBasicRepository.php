<?php

namespace App\Infrastructure\Repositories\EquipmentBasic;

use App\Domain\EquipmentBasic\Repositories\EquipmentBasicRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstEquipmentBasic;
use App\Domain\EquipmentBasic\Entities\EquipmentBasic;
use App\Domain\Common\Constants\CacheConstants;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EloquentEquipmentBasicRepository implements EquipmentBasicRepositoryInterface
{
    public function getAll(): Collection
    {
        return Cache::remember(CacheConstants::KEY_EQUIPMENT_BASIC, CacheConstants::TTL_MST, function () {
            return MstEquipmentBasic::where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn($model) => new EquipmentBasic(
                    id:        $model->id,
                    value:     $model->value,
                    label:     $model->label,
                    sortOrder: $model->sort_order,
                    isActive:  $model->is_active,
                ));
        });
    }
}
