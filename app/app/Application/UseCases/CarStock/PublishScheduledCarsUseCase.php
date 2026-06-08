<?php

declare(strict_types=1);

namespace App\Application\UseCases\CarStock;

use App\Domain\CarStock\Repositories\CarStockRepositoryInterface;

class PublishScheduledCarsUseCase
{
    public function __construct(
        private readonly CarStockRepositoryInterface $repository,
    ) {}

    public function execute(): array
    {
        // 公開開始
        $toPublish = $this->repository->findScheduledToPublish();
        foreach ($toPublish as $car) {
            $this->repository->publish($car);
        }

        // 公開終了
        $toUnpublish = $this->repository->findToUnpublish();
        foreach ($toUnpublish as $car) {
            $this->repository->unpublish($car);
        }

        return [
            'published'   => $toPublish->count(),
            'unpublished' => $toUnpublish->count(),
        ];
    }
}