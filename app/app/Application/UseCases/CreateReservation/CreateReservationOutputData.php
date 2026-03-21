<?php

namespace App\Application\UseCases\CreateReservation;

use App\Domain\CreateReservation\Entities\Reservation;

class CreateReservationOutputData
{
    public function __construct(
        private readonly Reservation $reservation,
    ) {}

    public function toArray(): array
    {
        return [
            'success'     => true,
            'message'     => '仮予約を受け付けました。担当のものが内容を確認後、ご連絡いたします。しばらくお待ちください。',
            'reservation' => (fn(Reservation $reservation) => $reservation->toArray())($this->reservation),
        ];
    }
}