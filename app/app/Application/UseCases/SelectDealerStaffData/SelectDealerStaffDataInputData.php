<?php

declare(strict_types=1);

namespace App\Application\UseCases\SelectDealerStaffData;

use App\Domain\SelectDealerStaffData\ValueObjects\DealerId;

final class SelectDealerStaffDataInputData
{
    public function __construct(
        public readonly DealerId $dealerId,
    ) {}
}