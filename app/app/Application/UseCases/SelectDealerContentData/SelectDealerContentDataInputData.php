<?php

declare(strict_types=1);

namespace App\Application\UseCases\SelectDealerContentData;

use App\Domain\SelectDealerContentData\ValueObjects\DealerId;

final class SelectDealerContentDataInputData
{
    public function __construct(
        public readonly DealerId $dealerId,
    ) {}
}