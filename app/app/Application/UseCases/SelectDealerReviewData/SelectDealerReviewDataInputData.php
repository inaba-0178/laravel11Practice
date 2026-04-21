<?php

declare(strict_types=1);

namespace App\Application\UseCases\SelectDealerReviewData;

use App\Domain\SelectDealerReviewData\ValueObjects\DealerId;

final class SelectDealerReviewDataInputData
{
    public function __construct(
        public readonly DealerId $dealerId,
    ) {}
}