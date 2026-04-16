<?php

declare(strict_types=1);

namespace App\Domain\SelectDealerStaffData\Repositories;

use App\Domain\SelectDealerStaffData\Entities\DealerStaff;
use App\Domain\SelectDealerStaffData\ValueObjects\DealerId;

interface DealerStaffRepositoryInterface
{
    public function findByDealerId(DealerId $dealerId): array;
}