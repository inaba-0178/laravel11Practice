<?php

declare(strict_types=1);

namespace App\Domain\SelectDealerAffiliatedData\Repositories;

use App\Domain\SelectDealerAffiliatedData\ValueObjects\DealerId;

interface AffiliatedStoreRepositoryInterface
{
    public function findByDealerId(DealerId $dealerId): array;
}