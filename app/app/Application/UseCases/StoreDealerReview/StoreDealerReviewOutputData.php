<?php

declare(strict_types=1);

namespace App\Application\UseCases\StoreDealerReview;

final class StoreDealerReviewOutputData
{
    public function __construct(
        private readonly int $reviewId,
    ) {}

    public function toArray(): array
    {
        return [
            'success'  => true,
            'reviewId' => $this->reviewId,
        ];
    }
}