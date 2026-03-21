<?php

namespace App\Application\UseCases\SelectDealerSchedule;

use App\Domain\SelectDealerSchedule\Repositories\DealerScheduleRepositoryInterface;

class SelectDealerScheduleUseCase
{
    public function __construct(
        private readonly DealerScheduleRepositoryInterface $scheduleRepository,
    ) {}

    public function execute(int $dealerId, int $reservationTypeId, string $month): SelectDealerScheduleOutputData
    {
        $schedules = $this->scheduleRepository->findByDealerAndMonth(
            $dealerId,
            $reservationTypeId,
            $month,
        );

        return new SelectDealerScheduleOutputData($schedules);
    }
}