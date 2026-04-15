<?php

declare(strict_types=1);

namespace App\Domain\SelectDealerInfoData\Repositories;

use App\Domain\SelectDealerInfoData\Entities\Dealer;
use App\Domain\SelectDealerInfoData\Exceptions\DealerNotFoundException;

interface DealerRepositoryInterface
{
    /**
     * @throws DealerNotFoundException
     */
    public function findById(int $dealerId): Dealer;
}