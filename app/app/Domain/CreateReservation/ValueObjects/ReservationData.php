<?php

namespace App\Domain\CreateReservation\ValueObjects;

final class ReservationData
{
    public function __construct(
        public readonly int     $dealerId,
        public readonly int     $carId,
        public readonly ?string $memberId,
        public readonly int     $reservationTypeId,
        public readonly int     $scheduleId,
        public readonly int     $maxReservations,
        public readonly ?string $memo,
        public readonly ?string $guestName,
        public readonly ?string $guestPhone,
        public readonly ?string $guestEmail,
        public readonly ?string $guestAddress,
    ) {}

    public function toArray(): array
    {
        return [
            'dealer_id'           => $this->dealerId,
            'car_id'              => $this->carId,
            'member_id'           => $this->memberId,
            'reservation_type_id' => $this->reservationTypeId,
            'schedule_id'         => $this->scheduleId,
            'memo'                => $this->memo,
            'guest_name'          => $this->guestName,
            'guest_phone'         => $this->guestPhone,
            'guest_email'         => $this->guestEmail,
            'guest_address'       => $this->guestAddress,
        ];
    }
}