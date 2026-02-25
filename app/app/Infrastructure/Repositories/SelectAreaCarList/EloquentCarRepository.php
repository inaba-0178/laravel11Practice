<?php
namespace App\Infrastructure\Repositories\SelectAreaCarList;

use App\Domain\SelectAreaCarList\Repositories\CarRepositoryInterface;
use App\Domain\SelectAreaCarList\Entities\Car;
use App\Domain\SelectAreaCarList\Exceptions\SelectAreaCarNotFoundException;
use App\Infrastructure\Eloquent\Opr\OprCars;
use App\Infrastructure\Repositories\BaseRepository;

class EloquentCarRepository extends BaseRepository implements CarRepositoryInterface
{
    public function __construct(
        OprCars $model
    ) {
        parent::__construct($model);
    }

    /**
     * @throws SelectAreaCarNotFoundException
     */
    public function findBySeriesId(int $seriesId, array $regionIds, int $offSet, int $limit): array
    {
        $cars = $this->model
            ->where('series_id', $seriesId)
            ->whereIn('region_id', $regionIds)
            ->offset($offSet)
            ->limit($limit)
            ->get();

        return $this->toEntities($cars, fn($car) => $this->toEntity($car));
    }

    private function toEntity(OprCars $model): Car
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