<?php

declare(strict_types=1);

namespace App\Application\UseCases\SelectDealerReviewData;

final class SelectDealerReviewDataOutputData
{
    public function __construct(
        private readonly array $reviews,
        private readonly int   $totalCount,
    ) {}

    public function toArray(): array
    {
        return [
            'success'    => true,
            'reviews'    => array_map(fn ($review) => $review->toArray(), $this->reviews),
            'totalCount' => $this->totalCount,
        ];
    }
}