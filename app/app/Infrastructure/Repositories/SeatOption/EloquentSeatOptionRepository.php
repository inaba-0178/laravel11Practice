<?php

namespace App\Infrastructure\Repositories\SeatOption;

use App\Domain\SeatOption\Repositories\SeatOptionRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstSeatOption;
use App\Domain\SeatOption\Entities\SeatOption;
use App\Domain\Common\Constants\CacheConstants;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EloquentSeatOptionRepository implements SeatOptionRepositoryInterface
{
    public function getAll(): Collection
    {
        return Cache::remember(CacheConstants::KEY_SEAT_OPTIONS, CacheConstants::TTL_MST, function () {
            return MstSeatOption::where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn($model) => new SeatOption(
                    id:        $model->id,
                    value:     $model->value,
                    label:     $model->label,
                    sortOrder: $model->sort_order,
                    isActive:  $model->is_active,
                ));
        });
    }
}
