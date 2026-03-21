<?php

namespace App\Infrastructure\Repositories\SelectReservationType;

use App\Domain\SelectReservationType\Repositories\ReservationTypeRepositoryInterface;
use App\Domain\SelectReservationType\Entities\ReservationType;
use App\Infrastructure\Eloquent\User\StkDealerReservationTypes;
use App\Infrastructure\Eloquent\Opr\OprReservationTypes;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class EloquentReservationTypeRepository extends BaseRepository implements ReservationTypeRepositoryInterface
{
    public function __construct(
        private readonly StkDealerReservationTypes $dealerReservationModel,
        private readonly OprReservationTypes       $reservationTypeModel,
    ) {
        parent::__construct($dealerReservationModel);
    }

    public function findByDealerId(int $dealerId): Collection
    {
        // ディーラーの許可済み予約種別IDを取得
        $allowedTypeIds = $this->dealerReservationModel
            ->where('dealer_id', $dealerId)
            ->where('is_active', 1)
            ->pluck('reservation_type_id')
            ->toArray();

        if (empty($allowedTypeIds)) {
            return collect();
        }

        // opr_reservation_types から種別情報を取得
        return $this->reservationTypeModel
            ->whereIn('id', $allowedTypeIds)
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($model) => $this->toEntity($model));
    }

    private function toEntity(OprReservationTypes $model): ReservationType
    {
        return new ReservationType(
            id          : $model->id,
            name        : $model->name,
            code        : $model->code,
            description : $model->description,
            isActive    : $model->is_active,
            sortOrder   : $model->sort_order,
        );
    }
}