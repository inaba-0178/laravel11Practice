<?php

namespace App\Application\UseCases\SelectReservationType;

use App\Domain\SelectReservationType\Entities\ReservationType;
use Illuminate\Support\Collection;

class SelectReservationTypeOutputData
{
    public function __construct(
        private readonly Collection $reservationTypes,
    ) {}

    public function toArray(): array
    {
        return [
            'success'          => true,
            'reservationTypes' => $this->reservationTypes
                ->map(fn(ReservationType $type) => $type->toArray())
                ->values()
                ->toArray(),
        ];
    }
}