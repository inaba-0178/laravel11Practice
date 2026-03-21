<?php

namespace App\Providers;

use App\Domain\SelectDealerSchedule\Repositories\DealerScheduleRepositoryInterface;
use App\Infrastructure\Repositories\SelectDealerSchedule\EloquentDealerScheduleRepository;
use Illuminate\Support\ServiceProvider;

class SelectDealerScheduleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            DealerScheduleRepositoryInterface::class,
            EloquentDealerScheduleRepository::class,
        );
    }
}