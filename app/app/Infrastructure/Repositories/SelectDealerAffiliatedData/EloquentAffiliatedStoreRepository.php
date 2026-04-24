<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\SelectDealerAffiliatedData;

use App\Domain\SelectDealerAffiliatedData\Entities\AffiliatedStore;
use App\Domain\SelectDealerAffiliatedData\Repositories\AffiliatedStoreRepositoryInterface;
use App\Domain\SelectDealerAffiliatedData\ValueObjects\DealerId;
use App\Infrastructure\Eloquent\User\StkAffiliatedStore;
use App\Constants\AffiliatedStoreStatus;
use App\Constants\AffiliatedStoreType;
use Illuminate\Support\Facades\Storage;

class EloquentAffiliatedStoreRepository implements AffiliatedStoreRepositoryInterface
{
    public function __construct(
        private readonly StkAffiliatedStore $model,
    ) {}

    public function findByDealerId(DealerId $dealerId): array
    {
        $id = $dealerId->getValue();

        // 申請元・申請先両方から承認済みを取得
        $records = $this->model
            ->where('status', AffiliatedStoreStatus::APPROVED)
            ->where(function ($q) use ($id) {
                $q->where('dealer_id', $id)
                  ->orWhere('affiliated_dealer_id', $id);
            })
            ->with(['dealer.mainImage', 'affiliatedDealer.mainImage'])
            ->get();

        return $records->map(function ($record) use ($id) {
            // 相手店舗を取得
            $targetDealer = $record->dealer_id === $id
                ? $record->affiliatedDealer
                : $record->dealer;

            $imageUrl = $targetDealer?->mainImage?->image_path
                ? Storage::disk('s3')->url($targetDealer->mainImage->image_path)
                : null;

            $address = ($targetDealer?->city ?? '') . ($targetDealer?->address_detail ?? '');

            return new AffiliatedStore(
                id                 : $record->id,
                dealerId           : $targetDealer?->id ?? 0,
                name               : $targetDealer?->name ?? '-',
                imageUrl           : $imageUrl,
                address            : $address,
                phone              : $targetDealer?->phone,
                businessHoursFrom  : $targetDealer?->business_hours_from,
                businessHoursTo    : $targetDealer?->business_hours_to,
                regularHolidayDays : $targetDealer?->regular_holiday_days,
                type               : $record->type,
                typeLabel          : AffiliatedStoreType::label($record->type),
            );
        })->toArray();
    }
}