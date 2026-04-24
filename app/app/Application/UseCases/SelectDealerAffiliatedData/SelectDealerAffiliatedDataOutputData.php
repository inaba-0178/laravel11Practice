<?php

declare(strict_types=1);

namespace App\Application\UseCases\SelectDealerAffiliatedData;

final class SelectDealerAffiliatedDataOutputData
{
    public function __construct(
        private readonly array $affiliatedStores,
    ) {}

    public function toArray(): array
    {
        return [
            'success'         => true,
            'affiliatedStores' => array_map(fn ($store) => $store->toArray(), $this->affiliatedStores),
        ];
    }
}