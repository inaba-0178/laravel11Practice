<?php
namespace App\Infrastructure\Repositories\SelectCarData;

use App\Domain\SelectCarData\Repositories\CarOptionRepositoryInterface;
use App\Domain\SelectCarData\Entities\CarOption;
use App\Domain\SelectCarData\Exceptions\CarNotFoundException;
use App\Infrastructure\Eloquent\User\StkCarOptions;
use App\Infrastructure\Repositories\BaseRepository;

class EloquentCarOptionRepository extends BaseRepository implements CarOptionRepositoryInterface
{
    public function __construct(StkCarOptions $model)
    {
        parent::__construct($model);
    }

    /**
     * @throws CarNotFoundException
     */
    public function findByCarId(int $carId): array
    {
        $carOptions = $this->model
            ->where('car_id', $carId)
            ->get();

        return $this->toEntities($carOptions, fn($model) => $this->toEntity($model));
    }

    private function toEntity(StkCarOptions $model): CarOption
    {
        return new CarOption(
            id              : $model->id,
            carId           : $model->car_id,
            optionCategory  : $model->option_category,
            optionName      : $model->option_name,
            isEquipped      : $model->is_equipped,
            displayOrder    : $model->display_order,
            
        );
    }
}