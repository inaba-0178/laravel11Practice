<?php

declare(strict_types=1);

namespace App\Domain\Asset\Repositories;

use App\Domain\Asset\Entities\Asset;
use App\Domain\Asset\ValueObjects\AssetType;

interface AssetUploadRepositoryInterface
{
    public function findRecord(AssetType $type, int $recordId): ?object;
    public function save(AssetType $type, int $recordId, string $storedPath): Asset;
}