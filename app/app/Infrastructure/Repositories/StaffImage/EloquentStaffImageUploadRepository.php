<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\StaffImage;

use App\Domain\StaffImage\Entities\StaffImage;
use App\Domain\StaffImage\Repositories\StaffImageUploadRepositoryInterface;
use App\Domain\StaffImage\ValueObjects\StaffId;
use App\Infrastructure\Eloquent\User\StkDealerStaff;

class EloquentStaffImageUploadRepository implements StaffImageUploadRepositoryInterface
{
    public function __construct(
        private readonly StkDealerStaff $model,
    ) {}

    public function save(StaffId $staffId, string $storedPath): StaffImage
    {
        $staff = $this->model->findOrFail($staffId->getValue());
        $staff->update(['image_path' => $storedPath]);

        return $this->toEntity($staff);
    }

    public function findById(StaffId $staffId): ?StaffImage
    {
        $staff = $this->model
            ->whereNull('deleted_at')
            ->find($staffId->getValue());

        if ($staff === null) return null;

        return $this->toEntity($staff);
    }

    private function toEntity(StkDealerStaff $model): StaffImage
    {
        return new StaffImage(
            id        : $model->id,
            dealerId  : $model->dealer_id,
            userId    : $model->user_id,
            imagePath : $model->image_path ?? '',
        );
    }
}