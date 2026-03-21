<?php

namespace App\Domain\SelectDealerSchedule\Entities;

final class DealerSchedule
{
    public function __construct(
        public readonly int     $id,
        public readonly int     $dealerId,
        public readonly int     $reservationTypeId,
        public readonly string  $date,
        public readonly string  $timeFrom,
        public readonly string  $timeTo,
        public readonly int     $maxReservations,
        public readonly int     $currentReservations,
        public readonly int     $isAvailable,
    ) {}

    public function getId(): int { return $this->id; }

    public function toArray(): array
    {
        return [
            'id'                  => $this->id,
            'dealerId'            => $this->dealerId,
            'reservationTypeId'   => $this->reservationTypeId,
            'date'                => $this->date,
            'timeFrom'            => $this->timeFrom,
            'timeTo'              => $this->timeTo,
            'maxReservations'     => $this->maxReservations,
            'currentReservations' => $this->currentReservations,
            'isAvailable'         => $this->isAvailable,
            'isFull'              => $this->currentReservations >= $this->maxReservations,
        ];
    }
}