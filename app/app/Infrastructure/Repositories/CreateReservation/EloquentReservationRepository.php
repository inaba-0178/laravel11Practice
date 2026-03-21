<?php

namespace App\Infrastructure\Repositories\CreateReservation;

use App\Domain\CreateReservation\Repositories\ReservationRepositoryInterface;
use App\Domain\CreateReservation\Entities\Reservation;
use App\Infrastructure\Eloquent\User\StkReservation;
use App\Infrastructure\Repositories\BaseRepository;

class EloquentReservationRepository extends BaseRepository implements ReservationRepositoryInterface
{
    public function __construct(StkReservation $model)
    {
        parent::__construct($model);
    }

    public function create(array $data): Reservation
    {
        $reservation = $this->model->create($data);
        
        return $this->toEntity($reservation->fresh());
    }

    public function countByScheduleId(int $scheduleId): int
    {
        return $this->model
            ->where('schedule_id', $scheduleId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereNull('deleted_at')
            ->count();
    }

    private function toEntity(StkReservation $model): Reservation
    {
        return new Reservation(
            id                : $model->id,
            dealerId          : $model->dealer_id,
            carId             : $model->car_id,
            memberId          : $model->member_id,
            reservationTypeId : $model->reservation_type_id,
            scheduleId        : $model->schedule_id,
            status            : $model->status,
            memo              : $model->memo,
            guestName         : $model->guest_name,
            guestPhone        : $model->guest_phone,
            guestEmail        : $model->guest_email,
            guestAddress      : $model->guest_address,
        );
    }
}