<?php

declare(strict_types=1);

namespace App\Application\UseCases\StaffImage;

use App\Domain\StaffImage\Entities\StaffImage;

final class UploadStaffImageOutputData
{
    public function __construct(
        private readonly StaffImage $staffImage,
    ) {}

    public function toArray(): array
    {
        return [
            'success'     => true,
            'staffImage'  => $this->staffImage->toArray(),
        ];
    }
}