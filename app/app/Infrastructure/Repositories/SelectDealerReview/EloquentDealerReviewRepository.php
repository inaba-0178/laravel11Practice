<?php

namespace App\Infrastructure\Repositories\SelectDealerReview;

use App\Domain\SelectDealerReview\Repositories\DealerReviewRepositoryInterface;
use App\Domain\SelectDealerReview\Entities\DealerReview;
use App\Infrastructure\Eloquent\User\StkDealerReview;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class EloquentDealerReviewRepository extends BaseRepository implements DealerReviewRepositoryInterface
{
    public function __construct(StkDealerReview $model)
    {
        parent::__construct($model);
    }

    public function findByDealerId(int $dealerId): Collection
    {
        return $this->model
            ->where('dealer_id', $dealerId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($model) => $this->toEntity($model));
    }

    private function toEntity(StkDealerReview $model): DealerReview
    {
        return new DealerReview(
            id       : $model->id,
            dealerId : $model->dealer_id,
            memberId : $model->member_id,
            rating   : $model->rating,
            comment  : $model->comment,
            createdAt: $model->created_at ? new \DateTimeImmutable($model->created_at) : null,
        );
    }
}