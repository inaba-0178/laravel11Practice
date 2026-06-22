<?php

namespace App\Infrastructure\Repositories\DetailOption;

use App\Domain\DetailOption\Repositories\DetailOptionsRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstDetailOptions;
use App\Domain\DetailOption\Entities\DetailOption;
use App\Domain\Common\Constants\CacheConstants;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EloquentDetailOptionsRepository implements DetailOptionsRepositoryInterface
{
    public function getAll(): Collection
    {
        return Cache::remember(CacheConstants::KEY_DETAIL_OPTIONS, CacheConstants::TTL_MST, function () {
            return MstDetailOptions::where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn($model) => new DetailOption(
                    id:        $model->id,
                    value:     $model->value,
                    label:     $model->label,
                    sortOrder: $model->sort_order,
                    isActive:  $model->is_active,
                ));
        });
    }
}
