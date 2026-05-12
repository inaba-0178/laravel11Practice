<?php

namespace App\Infrastructure\Repositories\ColorOption;

use App\Domain\ColorOption\Repositories\ColorOptionsRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstColorOptions;
use App\Domain\ColorOption\Entities\ColorOption;
use Illuminate\Support\Collection;

class EloquentColorOptionsRepository implements ColorOptionsRepositoryInterface
{
    public function getAll(): Collection
    {
        return MstColorOptions::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($model) => new ColorOption(
                id:        $model->id,
                value:     $model->value,
                label:     $model->label,
                hexCode:   $model->hex_code,
                group:     $model->group,
                sortOrder: $model->sort_order,
                isActive:  $model->is_active,
            ));
    }
}