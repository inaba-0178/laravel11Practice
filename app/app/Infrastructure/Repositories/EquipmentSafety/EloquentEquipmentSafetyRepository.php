<?php

namespace App\Infrastructure\Repositories\EquipmentSafety;

use App\Domain\EquipmentSafety\Repositories\EquipmentSafetyRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstEquipmentSafety;
use App\Domain\EquipmentSafety\Entities\EquipmentSafety;
use App\Domain\Common\Constants\CacheConstants;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EloquentEquipmentSafetyRepository implements EquipmentSafetyRepositoryInterface
{
    public function getAll(): Collection
    {
        return Cache::remember(CacheConstants::KEY_EQUIPMENT_SAFETY, CacheConstants::TTL_MST, function () {
            return MstEquipmentSafety::where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn($model) => new EquipmentSafety(
                    id:        $model->id,
                    value:     $model->value,
                    label:     $model->label,
                    sortOrder: $model->sort_order,
                    isActive:  $model->is_active,
                ));
        });
    }
}
