<?php

namespace App\Application\UseCases\SelectDealerReview;

use App\Domain\SelectDealerReview\Entities\DealerReview;
use Illuminate\Support\Collection;

class SelectDealerReviewOutputData
{
    public function __construct(
        private readonly Collection $reviews,
    ) {}

    public function toArray(): array
    {
        return [
            'success' => true,
            'reviews' => $this->reviews
                ->map(fn(DealerReview $review) => $review->toArray())
                ->values()
                ->toArray(),
        ];
    }
}