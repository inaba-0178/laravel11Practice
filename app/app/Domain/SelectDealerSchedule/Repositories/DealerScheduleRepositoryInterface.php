<?php

namespace App\Domain\SelectDealerSchedule\Repositories;

use Illuminate\Support\Collection;

interface DealerScheduleRepositoryInterface
{
    /**
     * ディーラーの予約可能スケジュールを取得
     *
     * @param int    $dealerId
     * @param int    $reservationTypeId
     * @param string $month YYYY-MM
     * @return Collection
     */
    public function findByDealerAndMonth(int $dealerId, int $reservationTypeId, string $month): Collection;
}