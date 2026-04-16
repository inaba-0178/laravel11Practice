<?php

declare(strict_types=1);

namespace App\Application\UseCases\StaffImage;

use App\Domain\StaffImage\ValueObjects\StaffId;
use Illuminate\Http\UploadedFile;

final class UploadStaffImageInputData
{
    public function __construct(
        public readonly StaffId      $staffId,
        public readonly UploadedFile $file,
    ) {}
}