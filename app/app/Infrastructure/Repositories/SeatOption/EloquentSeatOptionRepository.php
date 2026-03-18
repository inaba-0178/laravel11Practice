<?php

namespace App\Infrastructure\Repositories\SeatOption;

use App\Domain\SeatOption\Repositories\SeatOptionRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstSeatOption;
use App\Domain\SeatOption\Entities\SeatOption;
use Illuminate\Support\Collection;

class EloquentSeatOptionRepository implements SeatOptionRepositoryInterface
{
    public function getAll(): Collection
    {
        return MstSeatOption::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($model) => new SeatOption(
                id:             $model->id,
                value:          $model->value,
                label:          $model->label,
                sortOrder:      $model->sort_order,
                isActive:       $model->is_active,
            ));
    }
}