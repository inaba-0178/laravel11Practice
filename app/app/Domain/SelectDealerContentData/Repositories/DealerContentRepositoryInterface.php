<?php

declare(strict_types=1);

namespace App\Domain\SelectDealerContentData\Repositories;

use App\Domain\SelectDealerContentData\Entities\DealerContent;
use App\Domain\SelectDealerContentData\ValueObjects\DealerId;

interface DealerContentRepositoryInterface
{
    public function findByDealerId(DealerId $dealerId): array;
}