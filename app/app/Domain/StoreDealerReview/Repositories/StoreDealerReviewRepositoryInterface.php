<?php

declare(strict_types=1);

namespace App\Domain\StoreDealerReview\Repositories;

use App\Infrastructure\Eloquent\User\StkDealerReview;

interface StoreDealerReviewRepositoryInterface
{
    public function store(array $data): StkDealerReview;
    public function updateDealerRating(int $dealerId): void;
}