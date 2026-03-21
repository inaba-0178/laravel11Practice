<?php

namespace App\Domain\CreateReservation\Repositories;

use App\Domain\CreateReservation\Entities\DealerSchedule;

interface DealerScheduleRepositoryInterface
{
    /**
     * スケジュールをIDで取得
     *
     * @param int $scheduleId
     * @return DealerSchedule|null
     */
    public function findById(int $scheduleId): ?DealerSchedule;
}