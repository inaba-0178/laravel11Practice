<?php

declare(strict_types=1);

namespace App\Domain\StaffImage\Repositories;

use App\Domain\StaffImage\Entities\StaffImage;
use App\Domain\StaffImage\ValueObjects\StaffId;

interface StaffImageUploadRepositoryInterface
{
    public function save(StaffId $staffId, string $storedPath): StaffImage;
    public function findById(StaffId $staffId): ?StaffImage;
}