<?php

declare(strict_types=1);

namespace App\Application\UseCases\SelectDealerAffiliatedData;

use App\Domain\SelectDealerAffiliatedData\ValueObjects\DealerId;

final class SelectDealerAffiliatedDataInputData
{
    public function __construct(
        public readonly DealerId $dealerId,
    ) {}
}