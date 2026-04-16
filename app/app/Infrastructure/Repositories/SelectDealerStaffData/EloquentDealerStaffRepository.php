<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\SelectDealerStaffData;

use App\Domain\SelectDealerStaffData\Entities\DealerStaff;
use App\Domain\SelectDealerStaffData\Repositories\DealerStaffRepositoryInterface;
use App\Domain\SelectDealerStaffData\ValueObjects\DealerId;
use App\Infrastructure\Eloquent\User\StkDealerStaff;
use Illuminate\Support\Facades\Storage;

class EloquentDealerStaffRepository implements DealerStaffRepositoryInterface
{
    public function __construct(
        private readonly StkDealerStaff $model,
    ) {}

    public function findByDealerId(DealerId $dealerId): array
    {
        return $this->model
            ->where('dealer_id', $dealerId->getValue())
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($staff) => new DealerStaff(
                id        : $staff->id,
                dealerId  : $staff->dealer_id,
                userId    : $staff->user_id,
                name      : $staff->name,
                position  : $staff->position,
                imageUrl  : $staff->image_path
                    ? Storage::disk('s3')->url($staff->image_path)
                    : null,
                comment   : $staff->comment,
                sortOrder : $staff->sort_order,
            ))
            ->toArray();
    }
}