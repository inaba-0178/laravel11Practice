<?php
namespace App\Infrastructure\Repositories\SelectCarData;

use App\Domain\SelectCarData\Repositories\CarRepositoryInterface;
use App\Domain\SelectCarData\Entities\Car;
use App\Domain\SelectCarData\Exceptions\CarNotFoundException;
use App\Domain\Common\Constants\CacheConstants;
use App\Infrastructure\Eloquent\User\StkCar;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Support\Facades\Cache;

class EloquentCarRepository extends BaseRepository implements CarRepositoryInterface
{
    public function __construct(StkCar $model)
    {
        parent::__construct($model);
    }

    /**
     * @throws CarNotFoundException
     */
    public function findById(int $carId): Car
    {
        return Cache::remember(CacheConstants::KEY_CAR_DETAIL . ':' . $carId, CacheConstants::TTL_CAR, function () use ($carId) {
            $car = $this->model->where('id', $carId)->first();

            if ($car === null) {
                throw new CarNotFoundException($carId);
            }

            return $this->toEntity($car);
        });
    }

    private function toEntity(StkCar $model): Car
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
            recycleFee        : $model->recycle_fee ? (int)$model->recycle_fee : null,
            dealerFeeId       : $model->dealer_fee_id ? (int)$model->dealer_fee_id : null,
            totalPrice        : null,
            priceWithTax      : null,
            miscFees          : null,
        );
    }
}