<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\SelectDealerList;

use App\Domain\SelectDealerList\Entities\DealerListItem;
use App\Domain\SelectDealerList\Repositories\DealerListRepositoryInterface;
use App\Domain\SelectDealerList\ValueObjects\DealerSearchCondition;
use App\Infrastructure\Eloquent\User\StkCarDealer;
use Illuminate\Support\Facades\Storage;

class EloquentDealerListRepository implements DealerListRepositoryInterface
{
    public function __construct(
        private readonly StkCarDealer $model,
    ) {}

    private function buildQuery(DealerSearchCondition $condition)
    {
        $query = $this->model
            ->where('is_active', true)
            ->whereNull('deleted_at');

        if ($condition->regionId) {
            $query->where('region_id', $condition->regionId);
        } elseif ($condition->areaId) {
            $query->where('area_code', $condition->areaId);
        }

        if ($condition->name) {
            $query->where('name', 'like', '%' . $condition->name . '%');
        }

        return $query;
    }

    public function findByCondition(DealerSearchCondition $condition): array
    {
        $offset = ($condition->page - 1) * $condition->perPage;

        return $this->buildQuery($condition)
            ->with(['mainImage'])
            ->withCount(['reviews as review_count_actual'])
            ->withAvg('reviews as rating_service_avg', 'rating_service')
            ->withAvg('reviews as rating_atmosphere_avg', 'rating_atmosphere')
            ->withAvg('reviews as rating_after_avg', 'rating_after')
            ->withAvg('reviews as rating_quality_avg', 'rating_quality')
            ->orderBy('review_count', 'desc')
            ->offset($offset)
            ->limit($condition->perPage)
            ->get()
            ->map(fn ($dealer) => new DealerListItem(
                id                 : $dealer->id,
                name               : $dealer->name,
                imageUrl           : $dealer->mainImage?->image_path
                    ? Storage::disk('s3')->url($dealer->mainImage->image_path)
                    : null,
                address            : ($dealer->city ?? '') . ($dealer->address_detail ?? ''),
                phone              : $dealer->phone,
                businessHoursFrom  : $dealer->business_hours_from,
                businessHoursTo    : $dealer->business_hours_to,
                regularHolidayDays : $dealer->regular_holiday_days,
                reviewRating       : $dealer->review_rating,
                reviewCount        : $dealer->review_count,
                ratingService      : $dealer->rating_service_avg
                    ? round((float) $dealer->rating_service_avg, 1)
                    : null,
                ratingAtmosphere   : $dealer->rating_atmosphere_avg
                    ? round((float) $dealer->rating_atmosphere_avg, 1)
                    : null,
                ratingAfter        : $dealer->rating_after_avg
                    ? round((float) $dealer->rating_after_avg, 1)
                    : null,
                ratingQuality      : $dealer->rating_quality_avg
                    ? round((float) $dealer->rating_quality_avg, 1)
                    : null,
                carCount           : $dealer->cars()->whereNull('deleted_at')->count(),
            ))
            ->toArray();
    }

    public function countByCondition(DealerSearchCondition $condition): int
    {
        return $this->buildQuery($condition)->count();
    }
}