<?php

declare(strict_types=1);

namespace App\Application\UseCases\SelectDealerReviewData;

use App\Domain\SelectDealerReviewData\Repositories\DealerReviewRepositoryInterface;

final class SelectDealerReviewDataUseCase
{
    public function __construct(
        private readonly DealerReviewRepositoryInterface $reviewRepository,
    ) {}

    public function execute(SelectDealerReviewDataInputData $input): SelectDealerReviewDataOutputData
    {
        $reviews    = $this->reviewRepository->findByDealerId($input->dealerId);
        $totalCount = $this->reviewRepository->countByDealerId($input->dealerId);

        return new SelectDealerReviewDataOutputData($reviews, $totalCount);
    }
}