<?php

namespace App\Infrastructure\Repositories\CreateReservation;

use App\Domain\CreateReservation\Repositories\DealerScheduleRepositoryInterface;
use App\Domain\CreateReservation\Entities\DealerSchedule;
use App\Infrastructure\Eloquent\User\StkDealerSchedule;
use App\Infrastructure\Repositories\BaseRepository;
use Carbon\Carbon;

class EloquentDealerScheduleRepository extends BaseRepository implements DealerScheduleRepositoryInterface
{
    public function __construct(StkDealerSchedule $model)
    {
        parent::__construct($model);
    }

    public function findById(int $scheduleId): ?DealerSchedule
    {
        $result = $this->model
            ->where('id', $scheduleId)
            ->first();

        if ($result === null) {
            return null;
        }

        return $this->toEntity($result);
    }

    private function toEntity(StkDealerSchedule $model): DealerSchedule
    {
        return new DealerSchedule(
            id                  : $model->id,
            dealerId            : $model->dealer_id,
            reservationTypeId   : $model->reservation_type_id,
            date                : Carbon::parse($model->date)->format('Y-m-d'),
            timeFrom            : $model->time_from,
            timeTo              : $model->time_to,
            maxReservations     : $model->max_reservations,
            currentReservations : (int)($model->current_reservations ?? 0),
            isAvailable         : $model->is_available,
        );
    }
}