<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\SelectConditionCarList;

use App\Domain\SelectConditionCarList\Repositories\CarRepositoryInterface;
use App\Infrastructure\Eloquent\User\StkCar;
use App\Infrastructure\Repositories\Common\BaseCarRepository;
use Illuminate\Database\Eloquent\Builder;

class EloquentCarRepository extends BaseCarRepository implements CarRepositoryInterface
{
    public function __construct(StkCar $model)
    {
        parent::__construct($model);
    }

    public function findByCondition(int $offset, int $limit, array $searchParams = [], string $sortKey = '', string $sortOrder = ''): array
    {
        $query     = $this->buildQuery($searchParams);
        $this->applySorting($query, $sortKey, $sortOrder);
        $cars      = $query->offset($offset)->limit($limit)->get();
        $bodyTypes = $this->getBodyTypes($cars);

        return $this->toEntities($cars, fn($car) => $this->toEntity($car, $bodyTypes));
    }

    public function findTotalCount(array $searchParams = []): int
    {
        return $this->buildQuery($searchParams)->count();
    }

    private function buildQuery(array $searchParams): Builder
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
                'stk_car_images.image_url as image_url',
            ])
            ->leftJoin('stk_car_details', 'stk_cars.id', '=', 'stk_car_details.car_id')
            ->leftJoin('stk_car_dealers', 'stk_cars.dealer_id', '=', 'stk_car_dealers.id')
            ->leftJoin('stk_car_images', function ($join) {
                $join->on('stk_cars.id', '=', 'stk_car_images.car_id')
                    ->where('stk_car_images.is_main', '=', 1);
            })
            ->where('stk_cars.status', 'available');

        $this->applySearchFilters($query, $searchParams);

        return $query;
    }
}