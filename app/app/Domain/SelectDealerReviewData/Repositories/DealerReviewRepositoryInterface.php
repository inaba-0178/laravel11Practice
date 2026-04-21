<?php

declare(strict_types=1);

namespace App\Domain\SelectDealerReviewData\Repositories;

use App\Domain\SelectDealerReviewData\Entities\DealerReview;
use App\Domain\SelectDealerReviewData\ValueObjects\DealerId;

interface DealerReviewRepositoryInterface
{
    public function findByDealerId(DealerId $dealerId): array;
    public function countByDealerId(DealerId $dealerId): int;
}