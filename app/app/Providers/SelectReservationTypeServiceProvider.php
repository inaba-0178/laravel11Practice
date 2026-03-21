<?php

namespace App\Providers;

use App\Domain\SelectReservationType\Repositories\ReservationTypeRepositoryInterface;
use App\Infrastructure\Repositories\SelectReservationType\EloquentReservationTypeRepository;
use Illuminate\Support\ServiceProvider;

class SelectReservationTypeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ReservationTypeRepositoryInterface::class,
            EloquentReservationTypeRepository::class,
        );
    }
}