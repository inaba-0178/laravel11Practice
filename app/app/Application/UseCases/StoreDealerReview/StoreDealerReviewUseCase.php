<?php

declare(strict_types=1);

namespace App\Application\UseCases\StoreDealerReview;

use App\Domain\StoreDealerReview\Repositories\StoreDealerReviewRepositoryInterface;
use App\Infrastructure\Eloquent\User\StkCarDealer;
use App\Domain\StoreDealerReview\Exceptions\DealerNotFoundException;

final class StoreDealerReviewUseCase
{
    public function __construct(
        private readonly StoreDealerReviewRepositoryInterface $repository,
    ) {}

    public function execute(StoreDealerReviewInputData $input): StoreDealerReviewOutputData
    {
        $dealer = StkCarDealer::whereNull('deleted_at')
            ->where('is_active', true)
            ->find($input->dealerId->getValue());

        if (!$dealer) {
            throw new DealerNotFoundException($input->dealerId->getValue());
        }

        $reviewId = $this->repository->store([
            'dealer_id'         => $input->dealerId->getValue(),
            'member_id'         => $input->memberId,
            'nickname'          => $input->nickname->getValue(),
            'rating'            => $input->rating,
            'rating_service'    => $input->ratingService,
            'rating_atmosphere' => $input->ratingAtmosphere,
            'rating_after'      => $input->ratingAfter,
            'rating_quality'    => $input->ratingQuality,
            'comment'           => $input->comment->getValue(),
            'purchased_car'     => $input->purchasedCar?->getValue(),
            'purchased_at'      => $input->purchasedAt?->getValue(),
            'guest_name'        => $input->guestName?->getValue(),
            'guest_phone'       => $input->guestPhone?->getValue(),
            'guest_email'       => $input->guestEmail?->getValue(),
        ]);

        $this->repository->updateDealerRating($input->dealerId->getValue());

        return new StoreDealerReviewOutputData($reviewId);
    }
}