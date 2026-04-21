<?php

declare(strict_types=1);

namespace App\Domain\SelectDealerReviewData\Entities;

final class DealerReviewReply
{
    public function __construct(
        public readonly int    $id,
        public readonly string $body,
        public readonly string $createdAt,
    ) {}

    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'body'      => $this->body,
            'createdAt' => $this->createdAt,
        ];
    }
}