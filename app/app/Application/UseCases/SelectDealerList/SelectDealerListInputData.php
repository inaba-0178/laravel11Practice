<?php

declare(strict_types=1);

namespace App\Application\UseCases\SelectDealerList;

use App\Domain\SelectDealerList\ValueObjects\DealerSearchCondition;

final class SelectDealerListInputData
{
    public function __construct(
        public readonly DealerSearchCondition $condition,
    ) {}
}