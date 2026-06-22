<?php

namespace App\Infrastructure\Repositories\ColorOption;

use App\Domain\ColorOption\Repositories\ColorOptionsRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstColorOptions;
use App\Domain\ColorOption\Entities\ColorOption;
use App\Domain\Common\Constants\CacheConstants;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EloquentColorOptionsRepository implements ColorOptionsRepositoryInterface
{
    public function getAll(): Collection
    {
        return Cache::remember(CacheConstants::KEY_COLOR_OPTIONS, CacheConstants::TTL_MST, function () {
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
        });
    }
}
