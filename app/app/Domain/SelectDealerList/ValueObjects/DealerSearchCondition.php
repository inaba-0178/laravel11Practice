<?php

declare(strict_types=1);

namespace App\Domain\SelectDealerList\ValueObjects;

final class DealerSearchCondition
{
    public function __construct(
        public readonly ?int    $areaId,
        public readonly ?int    $regionId,
        public readonly ?string $name,
        public readonly int     $page,
        public readonly int     $perPage = 20,
    ) {}
}