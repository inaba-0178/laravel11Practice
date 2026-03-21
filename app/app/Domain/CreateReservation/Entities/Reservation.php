<?php

namespace App\Domain\CreateReservation\Entities;

final class Reservation
{
    public function __construct(
        public readonly int     $id,
        public readonly int     $dealerId,
        public readonly int     $carId,
        public readonly ?string $memberId,
        public readonly int     $reservationTypeId,
        public readonly int     $scheduleId,
        public readonly ?string $status,
        public readonly ?string $memo,
        public readonly ?string $guestName,
        public readonly ?string $guestPhone,
        public readonly ?string $guestEmail,
        public readonly ?string $guestAddress,
    ) {}

    public function getId(): int { return $this->id; }

    public function toArray(): array
    {
        return [
            'id'                => $this->id,
            'dealerId'          => $this->dealerId,
            'carId'             => $this->carId,
            'memberId'          => $this->memberId,
            'reservationTypeId' => $this->reservationTypeId,
            'scheduleId'        => $this->scheduleId,
            'status'            => $this->status,
            'memo'              => $this->memo ?? '',
            'guestName'         => $this->guestName ?? '',
            'guestPhone'        => $this->guestPhone ?? '',
            'guestEmail'        => $this->guestEmail ?? '',
            'guestAddress'      => $this->guestAddress ?? '',
        ];
    }
}