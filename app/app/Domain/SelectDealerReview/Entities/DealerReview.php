<?php

namespace App\Domain\SelectDealerReview\Entities;

use DateTimeImmutable;

final class DealerReview
{
    public function __construct(
        public readonly int                $id,
        public readonly int                $dealerId,
        public readonly ?string            $memberId,
        public readonly int                $rating,
        public readonly ?string            $comment,
        public readonly ?DateTimeImmutable $createdAt,
    ) {}

    public function getId(): int { return $this->id; }
    public function getDealerId(): int { return $this->dealerId; }
    public function getMemberId(): ?string { return $this->memberId; }
    public function getRating(): int { return $this->rating; }
    public function getComment(): ?string { return $this->comment; }
    public function getCreatedAt(): ?DateTimeImmutable { return $this->createdAt; }

    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'dealerId'  => $this->dealerId,
            'rating'    => $this->rating,
            'comment'   => $this->comment ?? '',
            'created_at'=> $this->createdAt?->format('Y-m-d H:i:s'),
        ];
    }
}