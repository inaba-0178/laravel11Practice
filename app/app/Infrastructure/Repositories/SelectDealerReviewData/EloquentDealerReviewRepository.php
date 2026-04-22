<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\SelectDealerReviewData;

use App\Domain\SelectDealerReviewData\Entities\DealerReview;
use App\Domain\SelectDealerReviewData\Entities\DealerReviewReply;
use App\Domain\SelectDealerReviewData\Repositories\DealerReviewRepositoryInterface;
use App\Domain\SelectDealerReviewData\ValueObjects\DealerId;
use App\Infrastructure\Eloquent\User\StkDealerReview;

class EloquentDealerReviewRepository implements DealerReviewRepositoryInterface
{
    public function __construct(
        private readonly StkDealerReview $model,
    ) {}

    public function findByDealerId(DealerId $dealerId): array
    {
        return $this->model
            ->where('dealer_id', $dealerId->getValue())
            ->whereNull('deleted_at')
            ->with(['member', 'activeReplies'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($review) => new DealerReview(
                id               : $review->id,
                dealerId         : $review->dealer_id,
                nickname         : $review->nickname,
                rating           : $review->rating,
                ratingService    : $review->rating_service,
                ratingAtmosphere : $review->rating_atmosphere,
                ratingAfter      : $review->rating_after,
                ratingQuality    : $review->rating_quality,
                comment          : $review->comment,
                purchasedCar     : $review->purchased_car,
                purchasedAt      : $review->purchased_at,
                createdAt        : $review->created_at->format('Y/m/d'),
                replies          : $review->activeReplies
                    ->map(fn ($reply) => new DealerReviewReply(
                        id        : $reply->id,
                        body      : $reply->body,
                        createdAt : $reply->created_at->format('Y/m/d'),
                    ))
                    ->toArray(),
            ))
            ->toArray();
    }

    public function countByDealerId(DealerId $dealerId): int
    {
        return $this->model
            ->where('dealer_id', $dealerId->getValue())
            ->whereNull('deleted_at')
            ->count();
    }
}