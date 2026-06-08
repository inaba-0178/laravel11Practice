<?php

declare(strict_types=1);

namespace App\Domain\CarStock\Repositories;

use App\Infrastructure\Eloquent\User\StkCar;
use Illuminate\Support\Collection;

interface CarStockRepositoryInterface
{
    public function findScheduledToPublish(): Collection;
    public function findToUnpublish(): Collection;
    public function publish(StkCar $car): void;
    public function unpublish(StkCar $car): void;
}