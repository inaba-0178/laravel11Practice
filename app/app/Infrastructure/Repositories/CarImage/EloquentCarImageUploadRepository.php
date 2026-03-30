<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\CarImage;

use App\Domain\CarImage\Entities\CarImage;
use App\Domain\CarImage\Repositories\CarImageUploadRepositoryInterface;
use App\Infrastructure\Eloquent\User\StkCarImages;

class EloquentCarImageUploadRepository implements CarImageUploadRepositoryInterface
{
    public function __construct(
        private readonly StkCarImages $model,
    ) {}

    public function save(
        int    $carId,
        string $storedPath,
        string $imageType,
        int    $displayOrder,
    ): CarImage {
        $record = $this->model->create([
            'car_id'        => $carId,
            'image_url'     => $storedPath,
            'image_type'    => $imageType,
            'display_order' => $displayOrder,
            'is_main'       => 0,
        ]);

        return $this->toEntity($record);
    }

    public function getMaxDisplayOrder(int $carId): int
    {
        return (int) $this->model
            ->where('car_id', $carId)
            ->max('display_order') ?? 0;
    }

    private function toEntity(StkCarImages $model): CarImage
    {
        return new CarImage(
            id           : $model->id,
            carId        : $model->car_id,
            imageUrl     : $model->image_url ?? '',
            imageType    : $model->image_type,
            displayOrder : $model->display_order,
            isMain       : (int) $model->is_main,  // キャストを追加
        );
    }
}