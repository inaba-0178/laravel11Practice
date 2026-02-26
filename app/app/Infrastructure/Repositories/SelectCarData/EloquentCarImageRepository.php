<?php
namespace App\Infrastructure\Repositories\SelectCarData;

use App\Domain\SelectCarData\Repositories\CarImageRepositoryInterface;
use App\Domain\SelectCarData\Entities\CarImage;
use App\Domain\SelectCarData\Exceptions\CarNotFoundException;
use App\Infrastructure\Eloquent\Opr\OprCarImages;
use App\Infrastructure\Repositories\BaseRepository;

class EloquentCarImageRepository extends BaseRepository implements CarImageRepositoryInterface
{
    public function __construct(OprCarImages $model)
    {
        parent::__construct($model);
    }

    /**
     * @throws CarNotFoundException
     */
    public function findByCarId(int $carId): array
    {
        $carImages = $this->model
            ->where('car_id', $carId)
            ->get();
        
        return $this->toEntities($carImages, fn($model) => $this->toEntity($model));
    }

    private function toEntity(OprCarImages $model): CarImage
    {
        return new CarImage(
            id              : $model->id,
            carId           : $model->car_id,
            imageUrl        : $model->image_url ?? '',
            imageType       : $model->image_type,
            displayOrder    : $model->display_order,
            isMain          : $model->is_main,
        );
    }
}