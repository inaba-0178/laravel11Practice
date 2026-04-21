<?php

declare(strict_types=1);

namespace App\Domain\SelectDealerReviewData\Entities;

final class DealerReview
{
    public function __construct(
        public readonly int     $id,
        public readonly int     $dealerId,
        public readonly ?string $nickname,
        public readonly int     $rating,
        public readonly ?int    $ratingService,
        public readonly ?int    $ratingAtmosphere,
        public readonly ?int    $ratingAfter,
        public readonly ?int    $ratingQuality,
        public readonly ?string $comment,
        public readonly ?string $purchasedCar,
        public readonly ?string $purchasedAt,
        public readonly string  $createdAt,
        public readonly array   $replies,
    ) {}

    public function toArray(): array
    {
        return [
            'id'               => $this->id,
            'dealerId'         => $this->dealerId,
            'nickname'         => $this->nickname,
            'rating'           => $this->rating,
            'ratingService'    => $this->ratingService,
            'ratingAtmosphere' => $this->ratingAtmosphere,
            'ratingAfter'      => $this->ratingAfter,
            'ratingQuality'    => $this->ratingQuality,
            'comment'          => $this->comment,
            'purchasedCar'     => $this->purchasedCar,
            'purchasedAt'      => $this->purchasedAt,
            'createdAt'        => $this->createdAt,
            'replies'          => array_map(fn ($reply) => $reply->toArray(), $this->replies),
        ];
    }
}