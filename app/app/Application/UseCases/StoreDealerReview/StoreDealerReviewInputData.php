<?php

declare(strict_types=1);

namespace App\Application\UseCases\StoreDealerReview;

use App\Domain\StoreDealerReview\ValueObjects\DealerId;
use App\Domain\StoreDealerReview\ValueObjects\Nickname;
use App\Domain\StoreDealerReview\ValueObjects\PurchasedCar;
use App\Domain\StoreDealerReview\ValueObjects\PurchasedAt;
use App\Domain\StoreDealerReview\ValueObjects\ReviewComment;
use App\Domain\StoreDealerReview\ValueObjects\GuestName;
use App\Domain\StoreDealerReview\ValueObjects\GuestPhone;
use App\Domain\StoreDealerReview\ValueObjects\GuestEmail;

final class StoreDealerReviewInputData
{
    public function __construct(
        public readonly DealerId      $dealerId,
        public readonly Nickname      $nickname,
        public readonly int           $rating,
        public readonly ?int          $ratingService,
        public readonly ?int          $ratingAtmosphere,
        public readonly ?int          $ratingAfter,
        public readonly ?int          $ratingQuality,
        public readonly ReviewComment $comment,
        public readonly ?PurchasedCar $purchasedCar,
        public readonly ?PurchasedAt  $purchasedAt,
        public readonly ?string       $memberId,
        public readonly ?GuestName    $guestName,
        public readonly ?GuestPhone   $guestPhone,
        public readonly ?GuestEmail   $guestEmail,
    ) {}
}