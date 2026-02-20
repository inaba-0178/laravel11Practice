<?php
namespace App\Infrastructure\Repositories\CarList;

use App\Domain\CarList\Repositories\CarRepositoryInterface;
use App\Domain\CarList\Entities\Car;
use App\Domain\CarList\Exceptions\CarNotFoundException;
use App\Infrastructure\Eloquent\Opr\OprCars;
use Illuminate\Support\Collection;

class EloquentCarRepository implements CarRepositoryInterface
{
    public function __construct(
        private readonly OprCars $model
    ) {}

    /**
     * @throws CarNotFoundException
     */
    public function findBySeriesId(int $seriesId): Collection
    {
        $cars = $this->model
            ->where('series_id', $seriesId)
            ->get();

        return $cars->map(fn($car) => $this->toEntity($car)); // 追加
    }

    private function toEntity(OprCars $model): Car
    {
        return new Car(
            id                : $model->id,
            dealerId          : $model->dealer_id ?? 0,
            seriesId          : $model->series_id,
            vehicleId         : $model->vehicle_id,
            stockNumber       : $model->stock_number ?? '',
            status            : $model->status,
            price             : $model->price,
            priceDisplayType  : $model->price_display_type,
            modelYear         : $model->model_year,
            mileage           : $model->mileage,
            bodyTypeId        : $model->body_type_id,
            color             : $model->color,
            transmission      : $model->transmission ?? '',
            fuelType          : $model->fuel_type ?? '',
            regionId          : $model->region_id,
            repairHistory     : $model->repair_history,
            mainImageUrl      : $model->main_image_url ?? '',
            publishedAt       : $model->published_at?->toDateTimeImmutable(),
            soldAt            : $model->sold_at?->toDateTimeImmutable(),
        );
    }
}