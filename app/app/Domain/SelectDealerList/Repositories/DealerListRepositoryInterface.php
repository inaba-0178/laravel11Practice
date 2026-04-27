<?php

declare(strict_types=1);

namespace App\Domain\SelectDealerList\Repositories;

use App\Domain\SelectDealerList\ValueObjects\DealerSearchCondition;

interface DealerListRepositoryInterface
{
    public function findByCondition(DealerSearchCondition $condition): array;
    public function countByCondition(DealerSearchCondition $condition): int;
}