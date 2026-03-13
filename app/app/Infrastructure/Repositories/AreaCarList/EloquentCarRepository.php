<?php
namespace App\Infrastructure\Repositories\AreaCarList;

use App\Domain\AreaCarList\Repositories\CarRepositoryInterface;
use App\Domain\AreaCarList\Entities\Car;
use App\Domain\AreaCarList\Exceptions\AreaCarNotFoundException;
use App\Infrastructure\Eloquent\User\StkCar;

class EloquentCarRepository implements CarRepositoryInterface
{
    public function __construct(
        private readonly StkCar $model
    ) {}

    /**
     * @throws AreaCarNotFoundException
     */
    public function findBySeriesId(int $seriesId): array
    {
        $cars = $this->model
            ->where('series_id', $seriesId)
            ->get();

        return $cars->map(fn($car) => $this->toEntity($car))->all();
    }

    private function toEntity(StkCar $model): Car
    {
        return new Car(
            id                : $model->id,
            dealerId          : $model->dealer_id,
            seriesId          : $model->series_id,
            vehicleId         : $model->vehicle_id,
            stockNumber       : $model->stock_number,
            status            : $model->status,
            price             : $model->price,
            priceDisplayType  : $model->price_display_type,
            modelYear         : $model->model_year,
            mileage           : $model->mileage,
            bodyTypeId        : $model->body_type_id,
            color             : $model->color,
            transmission      : $model->transmission,
            fuelType          : $model->fuel_type,
            regionId          : $model->region_id,
            repairHistory     : $model->repair_history,
            mainImageUrl      : $model->main_image_url,
            publishedAt       : $model->published_at?->toDateTimeImmutable(),
            soldAt            : $model->sold_at?->toDateTimeImmutable(),
        );
    }
}