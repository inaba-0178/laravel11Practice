<?php

namespace App\Infrastructure\Repositories\EquipmentEnv;

use App\Domain\EquipmentEnv\Repositories\EquipmentEnvRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstEquipmentEnv;
use App\Domain\EquipmentEnv\Entities\EquipmentEnv;
use Illuminate\Support\Collection;

class EloquentEquipmentEnvRepository implements EquipmentEnvRepositoryInterface
{
    public function getAll(): Collection
    {
        return MstEquipmentEnv::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($model) => new EquipmentEnv(
                id:             $model->id,
                value:          $model->value,
                label:          $model->label,
                sortOrder:      $model->sort_order,
                isActive:       $model->is_active,
            ));
    }
}