<?php

namespace App\Providers;

use App\Domain\CreateReservation\Repositories\ReservationRepositoryInterface;
use App\Infrastructure\Repositories\CreateReservation\EloquentReservationRepository;
use App\Domain\CreateReservation\Repositories\DealerScheduleRepositoryInterface;
use App\Infrastructure\Repositories\CreateReservation\EloquentDealerScheduleRepository;
use Illuminate\Support\ServiceProvider;

class CreateReservationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ReservationRepositoryInterface::class,
            EloquentReservationRepository::class,
        );

        $this->app->bind(
            DealerScheduleRepositoryInterface::class,
            EloquentDealerScheduleRepository::class,
        );
    }
}