<?php

namespace App\Infrastructure\Repositories\SelectAreaCarList;

use App\Domain\SelectAreaCarList\Repositories\CarRepositoryInterface;
use App\Domain\SelectAreaCarList\Entities\Car;
use App\Domain\SelectAreaCarList\Exceptions\SelectAreaCarNotFoundException;
use App\Infrastructure\Eloquent\User\StkCar;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class EloquentCarRepository extends BaseRepository implements CarRepositoryInterface
{
    public function __construct(
        StkCar $model
    ) {
        parent::__construct($model);
    }

    public function findBySeriesId(int $seriesId, array $regionIds, int $offset, int $limit, array $searchParams = [], string $sortKey = '', string $sortOrder = ''): array
    {
        $query = $this->buildQuery($seriesId, $regionIds, $searchParams);
        $this->applySorting($query, $sortKey, $sortOrder);
        $cars = $query->offset($offset)->limit($limit)->get();

        // mst_body_typesをmst DBから別途取得
        $bodyTypeIds = $cars->pluck('body_type_id')->filter()->unique()->toArray();
        $bodyTypes = \DB::connection('mst')
            ->table('mst_body_types')
            ->whereIn('id', $bodyTypeIds)
            ->get()
            ->keyBy('id');

        return $this->toEntities($cars, fn($car) => $this->toEntity($car, $bodyTypes));
    }

    public function findByTotalCount(int $seriesId, array $regionIds, array $searchParams = []): int
    {
        return $this->buildQuery($seriesId, $regionIds, $searchParams)->count();
    }

    private function buildQuery(int $seriesId, array $regionIds, array $searchParams): Builder
    {
        $query = $this->model
            ->select([
                'stk_cars.*',
                'stk_car_details.inspection_expire_date',
                'stk_car_details.inspection_status',
                'stk_car_details.drive_system',
                'stk_car_details.displacement',
                'stk_car_details.steering_wheel',
                'stk_car_details.number_of_doors',
                'stk_car_details.slide_door',
                'stk_car_details.riding_capacity',
                'stk_car_dealers.name as dealer_name',
                'stk_car_dealers.city as dealer_city',
                'stk_car_dealers.review_rating as dealer_rating',
                'stk_car_dealers.review_count as dealer_review_count',
                'stk_car_dealers.review_count as dealer_review_count',
                'stk_car_images.image_url as image_url',
            ])
            ->leftJoin('stk_car_details', 'stk_cars.id', '=', 'stk_car_details.car_id')
            ->leftJoin('stk_car_dealers', 'stk_cars.dealer_id', '=', 'stk_car_dealers.id')
            ->leftJoin('stk_car_images', function ($join) {
                $join->on('stk_cars.id', '=', 'stk_car_images.car_id')
                    ->where('stk_car_images.is_main', '=', 1);
            })
            ->where('stk_cars.series_id', $seriesId)
            ->whereIn('stk_cars.region_id', $regionIds)
            ->where('stk_cars.status', 'available');

        $this->applySearchFilters($query, $searchParams);

        return $query;
    }

    private function applySearchFilters(Builder $query, array $searchParams): void
    {
        // 価格
        if (!empty($searchParams['priceFrom'])) {
            $query->where('stk_cars.price', '>=', $searchParams['priceFrom'] * 10000);
        }
        if (!empty($searchParams['priceTo'])) {
            $query->where('stk_cars.price', '<=', $searchParams['priceTo'] * 10000);
        }

        // 年式
        if (!empty($searchParams['yearFrom'])) {
            $query->where('stk_cars.model_year', '>=', $searchParams['yearFrom']);
        }
        if (!empty($searchParams['yearTo'])) {
            $query->where('stk_cars.model_year', '<=', $searchParams['yearTo']);
        }

        // 走行距離
        if (!empty($searchParams['mileageFrom'])) {
            $query->where('stk_cars.mileage', '>=', $searchParams['mileageFrom']);
        }
        if (!empty($searchParams['mileageTo'])) {
            $query->where('stk_cars.mileage', '<=', $searchParams['mileageTo']);
        }

        // ミッション
        if (!empty($searchParams['transmission'])) {
            $transmissions = explode(',', $searchParams['transmission']);
            $query->whereIn('stk_cars.transmission', $transmissions);
        }

        // 燃料（エンジン種別）
        if (!empty($searchParams['engineType'])) {
            $query->where('stk_cars.fuel_type', $searchParams['engineType']);
        }

        // 修復歴
        if (!empty($searchParams['options']) && in_array('no_repair', explode(',', $searchParams['options']))) {
            $query->where('stk_cars.repair_history', 'none');
        }

        // 本体色
        if (!empty($searchParams['colors'])) {
            $colors = explode(',', $searchParams['colors']);
            $query->whereIn('stk_cars.color', $colors);
        }

        // 排気量
        if (!empty($searchParams['engineFrom'])) {
            $query->where('stk_car_details.displacement', '>=', $searchParams['engineFrom']);
        }
        if (!empty($searchParams['engineTo'])) {
            $query->where('stk_car_details.displacement', '<=', $searchParams['engineTo']);
        }

        // 駆動方式
        if (!empty($searchParams['driveType'])) {
            $query->where('stk_car_details.drive_system', $searchParams['driveType']);
        }

        // ハンドル
        if (!empty($searchParams['handle'])) {
            $query->where('stk_car_details.steering_wheel', $searchParams['handle']);
        }

        // ドア数
        if (!empty($searchParams['doorCount'])) {
            $query->where('stk_car_details.number_of_doors', $searchParams['doorCount']);
        }

        // スライドドア
        if (!empty($searchParams['slideDoor'])) {
            $query->where('stk_car_details.slide_door', $searchParams['slideDoor']);
        }

        // 乗車定員
        if (!empty($searchParams['passengerCount'])) {
            $query->where('stk_car_details.riding_capacity', $searchParams['passengerCount']);
        }

        // 車検
        if (!empty($searchParams['inspectionRemaining'])) {
            $date = match($searchParams['inspectionRemaining']) {
                '6m' => now()->addMonths(6)->format('Y-m-d'),
                '1y' => now()->addYear()->format('Y-m-d'),
                '2y' => now()->addYears(2)->format('Y-m-d'),
                default => null,
            };
            if ($date) {
                $query->where('stk_car_details.inspection_expire_date', '>=', $date);
            }
        }

        // フリーワード
        if (!empty($searchParams['freeWord'])) {
            $query->where('stk_car_details.free_text', 'like', '%' . $searchParams['freeWord'] . '%');
        }

        // 国産・輸入車
        if (!empty($searchParams['carTypes'])) {
            $carTypes = explode(',', $searchParams['carTypes']);
            $query->whereHas('vehicle', function ($q) use ($carTypes) {
                if (in_array('domestic', $carTypes)) {
                    $q->where('country_code', 'JP');
                }
                if (in_array('import', $carTypes)) {
                    $q->where('country_code', '!=', 'JP');
                }
            });
        }

        // 特殊車両（福祉・寒冷地・キャンピング・商用・逆輸入）
        $specialTypes = ['welfare', 'cold_region', 'camping', 'commercial', 'reimport'];
        if (!empty($searchParams['carTypes'])) {
            $carTypes    = explode(',', $searchParams['carTypes']);
            $hasSpecial  = array_intersect($carTypes, $specialTypes);
            if (!empty($hasSpecial)) {
                $query->whereHas('options', function ($q) use ($hasSpecial) {
                    $q->where('option_category', 'special_type')
                      ->whereIn('option_name', $hasSpecial)
                      ->where('is_equipped', true);
                });
            }
        }
    }

    private function applySorting(Builder $query, string $sortKey, string $sortOrder): void
    {
        $order = $sortOrder === 'asc' ? 'asc' : 'desc';

        match($sortKey) {
            'publishedAt'  => $query->orderBy('stk_cars.published_at', $order),
            'price'        => $query->orderBy('stk_cars.price', $order),
            'modelYear'    => $query->orderBy('stk_cars.model_year', $order),
            'mileage'      => $query->orderBy('stk_cars.mileage', $order),
            'displacement' => $query->orderBy('stk_car_details.displacement', $order),
            'repairHistory'=> $query->orderBy('stk_cars.repair_history', $order),
            'inspection'   => $query->orderBy('stk_car_details.inspection_expire_date', $order),
            default        => $query->orderBy('stk_cars.published_at', 'desc'),
        };
    }

    private function toEntity(StkCar $model, $bodyTypes = null): Car
    {
        $publishedAt = $model->published_at
            ? new \DateTimeImmutable($model->published_at)
            : null;

        $isNew = $publishedAt
            ? $publishedAt >= new \DateTimeImmutable('-7 days')
            : false;

        return new Car(
            id                      : $model->id,
            dealerId                : $model->dealer_id,
            seriesId                : $model->series_id,
            vehicleId               : $model->vehicle_id,
            stockNumber             : $model->stock_number,
            status                  : $model->status,
            price                   : $model->price,
            priceDisplayType        : $model->price_display_type,
            modelYear               : $model->model_year,
            mileage                 : $model->mileage,
            bodyTypeId              : $model->body_type_id,
            color                   : $model->color,
            transmission            : $model->transmission,
            fuelType                : $model->fuel_type,
            regionId                : $model->region_id,
            repairHistory           : $model->repair_history,
            mainImageUrl            : $model->image_url,
            publishedAt             : $publishedAt,
            soldAt                  : $model->sold_at ? new \DateTimeImmutable($model->sold_at) : null,
            inspectionExpireDate    : $model->inspection_expire_date,
            inspectionStatus        : $model->inspection_status,
            driveSystem             : $model->drive_system,
            displacement            : $model->displacement,
            steeringWheel           : $model->steering_wheel,
            numberOfDoors           : $model->number_of_doors,
            slideDoor               : $model->slide_door,
            ridingCapacity          : $model->riding_capacity,
            dealerName              : $model->dealer_name,
            dealerCity              : $model->dealer_city,
            dealerRating            : $model->dealer_rating ? (float)$model->dealer_rating : null,
            dealerReviewCount       : $model->dealer_review_count ?? 0,
            bodyTypeName            : isset($bodyTypes[$model->body_type_id]) ? $bodyTypes[$model->body_type_id]->name : '',
            isNew                   : $isNew,
        );
    }
}