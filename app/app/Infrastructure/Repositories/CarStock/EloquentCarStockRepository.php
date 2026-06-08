<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\CarStock;

use App\Constants\CarStatus;
use App\Domain\CarStock\Repositories\CarStockRepositoryInterface;
use App\Infrastructure\Eloquent\User\StkCar;
use Illuminate\Support\Collection;

class EloquentCarStockRepository implements CarStockRepositoryInterface
{
    public function findScheduledToPublish(): Collection
    {
        return StkCar::where('status', CarStatus::SCHEDULED)
            ->where('published_at', '<=', now())
            ->get();
    }

    public function findToUnpublish(): Collection
    {
        return StkCar::where('status', CarStatus::AVAILABLE)
            ->whereNotNull('publish_end_at')
            ->where('publish_end_at', '<=', now())
            ->get();
    }

    public function publish(StkCar $car): void
    {
        $car->update(['status' => CarStatus::AVAILABLE]);
    }

    public function unpublish(StkCar $car): void
    {
        $car->update([
            'status'    => CarStatus::PUBLISH_ENDED,
        ]);
    }
}