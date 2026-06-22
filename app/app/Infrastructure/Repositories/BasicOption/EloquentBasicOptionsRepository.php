<?php

namespace App\Infrastructure\Repositories\BasicOption;

use App\Domain\BasicOption\Repositories\BasicOptionsRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstBasicOptions;
use App\Domain\BasicOption\Entities\BasicOption;
use App\Domain\Common\Constants\CacheConstants;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EloquentBasicOptionsRepository implements BasicOptionsRepositoryInterface
{
    public function getAll(): Collection
    {
        return Cache::remember(CacheConstants::KEY_BASIC_OPTIONS, CacheConstants::TTL_MST, function () {
            return MstBasicOptions::where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn($model) => new BasicOption(
                    id:             $model->id,
                    value:          $model->value,
                    label:          $model->label,
                    isHighlight:    $model->is_highlight,
                    sortOrder:      $model->sort_order,
                    isActive:       $model->is_active,
                ));
        });
    }
}