<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\SelectDealerInfoData;

use App\Domain\SelectDealerInfoData\Entities\DealerImage;
use App\Domain\SelectDealerInfoData\Repositories\DealerImageRepositoryInterface;
use App\Infrastructure\Eloquent\User\StkDealerImage;
use Illuminate\Support\Facades\Storage;

class EloquentDealerImageRepository implements DealerImageRepositoryInterface
{
    public function __construct(
        private readonly StkDealerImage $model,
    ) {}

    public function findByDealerId(int $dealerId): array
    {
        return $this->model
            ->where('dealer_id', $dealerId)
            ->whereNull('deleted_at')
            ->orderBy('is_main', 'desc')
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($img) => new DealerImage(
                id        : $img->id,
                dealerId  : $img->dealer_id,
                imageUrl  : Storage::disk('s3')->url($img->image_path),
                altText   : $img->alt_text,
                isMain    : $img->is_main,
                sortOrder : $img->sort_order,
                caption   : $img->text?->caption,
            ))
            ->toArray();
    }
}