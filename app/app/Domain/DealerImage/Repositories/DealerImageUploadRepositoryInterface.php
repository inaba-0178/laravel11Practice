<?php

declare(strict_types=1);

namespace App\Domain\DealerImage\Repositories;

use App\Domain\DealerImage\Entities\DealerImage;

interface DealerImageUploadRepositoryInterface
{
    public function save(
        int     $dealerId,
        string  $storedPath,
        ?string $altText,
        int     $sortOrder,
    ): DealerImage;

    public function getMaxSortOrder(int $dealerId): int;

    public function countByDealerId(int $dealerId): int;
}