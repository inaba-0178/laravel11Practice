<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\StoreDealerReview;

use App\Domain\StoreDealerReview\Repositories\StoreDealerReviewRepositoryInterface;
use App\Infrastructure\Eloquent\User\StkDealerReview;
use App\Infrastructure\Eloquent\User\StkCarDealer;

class EloquentStoreDealerReviewRepository implements StoreDealerReviewRepositoryInterface
{
    public function __construct(
        private readonly StkDealerReview $model,
        private readonly StkCarDealer    $dealerModel, // 追加
    ) {}

    public function store(array $data): int
    {
        return $this->model->create($data)->id; // ->id を追加
    }

    public function updateDealerRating(int $dealerId): void
    {
        $avg = $this->model
            ->where('dealer_id', $dealerId)
            ->whereNull('deleted_at')
            ->avg('rating');

        $count = $this->model
            ->where('dealer_id', $dealerId)
            ->whereNull('deleted_at')
            ->count();

        $this->dealerModel  // StkCarDealer:: → $this->dealerModel に変更
            ->where('id', $dealerId)
            ->update([
                'review_rating' => round((float) $avg, 2),
                'review_count'  => $count,
            ]);
    }

}