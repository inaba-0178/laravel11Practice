<?php

declare(strict_types=1);

namespace App\Application\UseCases\Asset;

use App\Domain\Asset\ValueObjects\AssetType;
use Illuminate\Http\UploadedFile;

final class UploadAssetInputData
{
    public function __construct(
        public readonly AssetType    $type,
        public readonly int          $recordId,
        public readonly UploadedFile $file,
    ) {}
}