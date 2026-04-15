<?php

declare(strict_types=1);

namespace App\Application\UseCases\SelectDealerInfoData;

use App\Domain\SelectDealerInfoData\Entities\Dealer;

final class SelectDealerInfoDataOutputData
{
    public function __construct(
        private readonly Dealer $dealer,
        private readonly array  $images,
    ) {}

    public function toArray(): array
    {
        return [
            'success'     => true,
            'dealerData'  => $this->dealer->toArray(),
            'dealerImages' => array_map(fn($image) => $image->toArray(), $this->images),
        ];
    }
}