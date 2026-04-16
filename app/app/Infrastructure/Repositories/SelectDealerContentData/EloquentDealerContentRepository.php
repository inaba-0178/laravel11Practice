<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\SelectDealerContentData;

use App\Domain\SelectDealerContentData\Entities\DealerContent;
use App\Domain\SelectDealerContentData\Repositories\DealerContentRepositoryInterface;
use App\Domain\SelectDealerContentData\ValueObjects\DealerId;
use App\Infrastructure\Eloquent\User\StkDealerContent;
use Illuminate\Support\Facades\Storage;

class EloquentDealerContentRepository implements DealerContentRepositoryInterface
{
    public function __construct(
        private readonly StkDealerContent $model,
    ) {}

    public function findByDealerId(DealerId $dealerId): array
    {
        return $this->model
            ->where('dealer_id', $dealerId->getValue())
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($content) => new DealerContent(
                id          : $content->id,
                dealerId    : $content->dealer_id,
                category    : $content->category,
                title       : $content->title,
                description : $content->description,
                imageUrl    : $content->image_path
                    ? Storage::disk('s3')->url($content->image_path)
                    : null,
                sortOrder   : $content->sort_order,
                startedAt   : $content->started_at?->format('Y/m/d'),
                endedAt     : $content->ended_at?->format('Y/m/d'),
            ))
            ->toArray();
    }
}