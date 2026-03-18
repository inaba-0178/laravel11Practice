<?php

namespace App\Infrastructure\Repositories\CarTypeOption;

use App\Domain\CarTypeOption\Repositories\CarTypeOptionRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstCarTypeOption;
use App\Domain\CarTypeOption\Entities\CarTypeOption;
use Illuminate\Support\Collection;

class EloquentCarTypeOptionRepository implements CarTypeOptionRepositoryInterface
{
    public function getAll(): Collection
    {
        return MstCarTypeOption::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($model) => new CarTypeOption(
                id:             $model->id,
                value:          $model->value,
                label:          $model->label,
                sortOrder:      $model->sort_order,
                isActive:       $model->is_active,
            ));
    }
}