<?php

declare(strict_types=1);

namespace App\Application\UseCases\Asset;

use App\Domain\Asset\Entities\Asset;

final class UploadAssetOutputData
{
    public function __construct(
        private readonly Asset $asset,
    ) {}

    public function toArray(): array
    {
        return [
            'success' => true,
            'asset'   => $this->asset->toArray(),
        ];
    }
}