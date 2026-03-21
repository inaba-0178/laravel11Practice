<?php

namespace App\Application\UseCases\SelectReservationType;

use App\Domain\SelectReservationType\Repositories\ReservationTypeRepositoryInterface;

class SelectReservationTypeUseCase
{
    public function __construct(
        private readonly ReservationTypeRepositoryInterface $reservationTypeRepository,
    ) {}

    public function execute(int $dealerId): SelectReservationTypeOutputData
    {
        $reservationTypes = $this->reservationTypeRepository->findByDealerId($dealerId);

        return new SelectReservationTypeOutputData($reservationTypes);
    }
}