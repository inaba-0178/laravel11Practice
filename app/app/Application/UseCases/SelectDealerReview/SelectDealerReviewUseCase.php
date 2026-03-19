<?php

namespace App\Application\UseCases\SelectDealerReview;

use App\Domain\SelectDealerReview\Repositories\DealerReviewRepositoryInterface;

class SelectDealerReviewUseCase
{
    public function __construct(
        private readonly DealerReviewRepositoryInterface $reviewRepository,
    ) {}

    public function execute(int $dealerId): SelectDealerReviewOutputData
    {
        $reviews = $this->reviewRepository->findByDealerId($dealerId);

        return new SelectDealerReviewOutputData($reviews);
    }
}