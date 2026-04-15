<?php

declare(strict_types=1);

namespace App\Application\UseCases\SelectDealerInfoData;

use App\Domain\SelectDealerInfoData\ValueObjects\DealerId;

final class SelectDealerInfoDataInputData
{
    public function __construct(
        public readonly DealerId $dealerId,
    ) {}
}