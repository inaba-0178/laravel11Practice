<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\DealerImage;

use App\Domain\DealerImage\Entities\DealerImage;
use App\Domain\DealerImage\Repositories\DealerImageUploadRepositoryInterface;
use App\Infrastructure\Eloquent\User\StkDealerImage;

class EloquentDealerImageUploadRepository implements DealerImageUploadRepositoryInterface
{
    public function __construct(
        private readonly StkDealerImage $model,
    ) {}

    public function save(
        int     $dealerId,
        string  $storedPath,
        ?string $altText,
        int     $sortOrder,
    ): DealerImage {
        $record = $this->model->create([
            'dealer_id'  => $dealerId,
            'image_path' => $storedPath,
            'alt_text'   => $altText,
            'is_main'    => false,
            'sort_order' => $sortOrder,
        ]);

        return $this->toEntity($record);
    }

    public function getMaxSortOrder(int $dealerId): int
    {
        return (int) $this->model
            ->where('dealer_id', $dealerId)
            ->max('sort_order') ?? 0;
    }

    public function countByDealerId(int $dealerId): int
    {
        return $this->model
            ->where('dealer_id', $dealerId)
            ->whereNull('deleted_at')
            ->count();
    }

    private function toEntity(StkDealerImage $model): DealerImage
    {
        return new DealerImage(
            id        : $model->id,
            dealerId  : $model->dealer_id,
            imagePath : $model->image_path,
            altText   : $model->alt_text,
            isMain    : (bool) $model->is_main,
            sortOrder : $model->sort_order,
        );
    }
}