<?php

declare(strict_types=1);

namespace App\Application\UseCases\CarImage;

use App\Domain\CarImage\ValueObjects\CarId;
use App\Domain\CarImage\ValueObjects\ImageType;
use Illuminate\Http\UploadedFile;

final class UploadCarImageInputData
{
    public function __construct(
        public readonly CarId        $carId,
        public readonly ImageType    $imageType,
        public readonly UploadedFile $file,
    ) {}
}