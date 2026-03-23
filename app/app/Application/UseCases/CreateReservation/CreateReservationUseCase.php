<?php

namespace App\Application\UseCases\CreateReservation;

use App\Domain\CreateReservation\Repositories\ReservationRepositoryInterface;
use App\Domain\CreateReservation\Exceptions\ScheduleFullException;
use App\Domain\CreateReservation\ValueObjects\ReservationData;
use App\Domain\Shared\Transactions\UserTransactionInterface;
use App\Domain\CreateReservation\Exceptions\PastTimeException;
use App\Domain\CreateReservation\Repositories\DealerScheduleRepositoryInterface;

class CreateReservationUseCase
{
    public function __construct(
        private readonly ReservationRepositoryInterface     $reservationRepository,
        private readonly UserTransactionInterface           $transaction,
        private readonly DealerScheduleRepositoryInterface  $scheduleRepository,
    ) {}

    public function execute(ReservationData $data): CreateReservationOutputData
    {
        // 過去時間チェック
        $schedule = $this->scheduleRepository->findById($data->scheduleId);

        if ($schedule) {
            $scheduleDateTime = new \DateTime("{$schedule->date} {$schedule->timeFrom}");
            if ($scheduleDateTime < new \DateTime()) {
                throw new PastTimeException();
            }
        }
        return $this->transaction->run(function () use ($data) {

            // スケジュールの空き確認（先取り防止）
            $currentCount = $this->reservationRepository->countByScheduleId($data->scheduleId);

            if ($currentCount >= $data->maxReservations) {
                throw new ScheduleFullException();
            }

            $reservation = $this->reservationRepository->create($data->toArray());

            return new CreateReservationOutputData($reservation);
        });
    }
}