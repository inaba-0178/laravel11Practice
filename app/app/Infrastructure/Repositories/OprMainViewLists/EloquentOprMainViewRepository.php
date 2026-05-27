<?php

namespace App\Infrastructure\Repositories\OprMainViewLists;

use App\Domain\OprMainViewLists\Entities\OprMainView;
use App\Domain\OprMainViewLists\Repositories\OprMainViewRepositoryInterface;
use App\Infrastructure\Eloquent\Opr\OprMainView as OprMainViewModel;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Support\Carbon;

class EloquentOprMainViewRepository extends BaseRepository implements OprMainViewRepositoryInterface
{
    public function __construct(OprMainViewModel $model)
    {
        parent::__construct($model);
    }

    public function findActive(): array
    {
        $now = Carbon::now();

        $views = $this->model
            ->where('is_active', 1)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
            })
            ->orderBy('sort_order')
            ->get();

        return $this->toEntities($views, fn($model) => $this->toEntity($model));
    }

    private function toEntity(OprMainViewModel $model): OprMainView
    {
        return new OprMainView(
            $model->id,
            $model->title,
            $model->sub,
            $model->label,
            $model->image_path,
            $model->link_url,
            $model->sort_order,
            $model->is_active,
            $model->start_at?->toDateTimeString(),
            $model->end_at?->toDateTimeString(),
        );
    }
}