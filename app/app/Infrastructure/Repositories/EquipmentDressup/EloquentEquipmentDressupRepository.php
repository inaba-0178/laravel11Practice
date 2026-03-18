<?php

namespace App\Infrastructure\Repositories\EquipmentDressup;

use App\Domain\EquipmentDressup\Repositories\EquipmentDressupRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstEquipmentDressup;
use App\Domain\EquipmentDressup\Entities\EquipmentDressup;
use Illuminate\Support\Collection;

class EloquentEquipmentDressupRepository implements EquipmentDressupRepositoryInterface
{
    public function getAll(): Collection
    {
        return MstEquipmentDressup::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($model) => new EquipmentDressup(
                id:             $model->id,
                value:          $model->value,
                label:          $model->label,
                sortOrder:      $model->sort_order,
                isActive:       $model->is_active,
            ));
    }
}