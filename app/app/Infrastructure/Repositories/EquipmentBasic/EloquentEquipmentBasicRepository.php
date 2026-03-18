<?php

namespace App\Infrastructure\Repositories\EquipmentBasic;

use App\Domain\EquipmentBasic\Repositories\EquipmentBasicRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstEquipmentBasic;
use App\Domain\EquipmentBasic\Entities\EquipmentBasic;
use Illuminate\Support\Collection;

class EloquentEquipmentBasicRepository implements EquipmentBasicRepositoryInterface
{
    public function getAll(): Collection
    {
        return MstEquipmentBasic::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($model) => new EquipmentBasic(
                id:             $model->id,
                value:          $model->value,
                label:          $model->label,
                sortOrder:      $model->sort_order,
                isActive:       $model->is_active,
            ));
    }
}