<?php

namespace App\Domain\CreateReservation\Repositories;

use App\Domain\CreateReservation\Entities\Reservation;

interface ReservationRepositoryInterface
{
    /**
     * 予約を登録する
     *
     * @param array $data
     * @return Reservation
     */
    public function create(array $data): Reservation;

    /**
     * スケジュールの現在の予約数を取得
     *
     * @param int $scheduleId
     * @return int
     */
    public function countByScheduleId(int $scheduleId): int;
}