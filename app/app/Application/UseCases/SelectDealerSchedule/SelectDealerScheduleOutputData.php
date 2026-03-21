<?php

namespace App\Application\UseCases\SelectDealerSchedule;

use App\Domain\SelectDealerSchedule\Entities\DealerSchedule;
use Illuminate\Support\Collection;

class SelectDealerScheduleOutputData
{
    public function __construct(
        private readonly Collection $schedules,
    ) {}

    public function toArray(): array
    {
        return [
            'success'   => true,
            'schedules' => $this->schedules
                ->map(fn(DealerSchedule $schedule) => $schedule->toArray())
                ->values()
                ->toArray(),
        ];
    }
}