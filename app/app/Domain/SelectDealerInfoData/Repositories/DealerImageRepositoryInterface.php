<?php

declare(strict_types=1);

namespace App\Domain\SelectDealerInfoData\Repositories;

use App\Domain\SelectDealerInfoData\Entities\DealerImage;

interface DealerImageRepositoryInterface
{
    public function findByDealerId(int $dealerId): array;
}