<?php

namespace App\Application\UseCases\SelectDealerData;

use App\Domain\SelectDealerData\Entities\Dealer;

class SelectDealerDataOutputData
{
    public function __construct(
        private readonly Dealer $dealer,
    ) {}

    public function toArray(): array
    {
        return [
            'success'    => true,
            'dealerData' => $this->dealer->toArray(),
        ];
    }
}