<?php

declare(strict_types=1);

namespace App\Application\UseCases\CarImage;

use App\Domain\CarImage\Entities\CarImage;

final class UploadCarImageOutputData
{
    public function __construct(
        private readonly CarImage $carImage,
    ) {}

    public function toArray(): array
    {
        return [
            'success'  => true,
            'carImage' => $this->carImage->toArray(),
        ];
    }
}