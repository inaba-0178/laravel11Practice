<?php
namespace App\Infrastructure\Repositories\CarList;

use App\Domain\CarList\Repositories\CarDetailRepositoryInterface;
use App\Domain\CarList\Entities\CarDetail;
use App\Domain\CarList\Exceptions\CarNotFoundException;
use App\Infrastructure\Eloquent\Opr\OprCarDetails;

class EloquentCarDetailRepository implements CarDetailRepositoryInterface
{
    public function __construct(
        private readonly OprCarDetails $model
    ) {}

    /**
     * @throws CarNotFoundException
     */
    public function findByCarId(array $carId): array
    {
        if (empty($carId)) {
            return [];
        }
        $carDetails = $this->model
            ->whereIn('car_id', $carId)
            ->get();
        
        return $this->toEntities($carDetails);
    }

    private function toEntity(OprCarDetails $model): CarDetail
    {
        return new CarDetail(
            id                      : $model->id,
            carId                   : $model->car_id ?? 0,
            firstRegistrationDate   : $model->first_registration_date ? new \DateTimeImmutable($model->first_registration_date) : null,
            inspectionExpireDate    : $model->inspection_expire_date ? new \DateTimeImmutable($model->inspection_expire_date) : null,
            inspectionStatus        : $model->inspection_status ?? '',
            driveSystem             : $model->drive_system,
            displacement            : $model->displacement,
            steeringWheel           : $model->steering_wheel,
            numberOfDoors           : $model->number_of_doors,
            slideDoor               : $model->slide_door,
            ridingCapacity          : $model->riding_capacity,
            loanAvailable           : $model->loan_available,
            description             : $model->description,
            freeText                : $model->free_text ?? '',
            seoTitle                : $model->seo_title ?? '',
            seoDescription          : $model->seo_description,
        );
    }

    /**
     * @return CarDetail[]
     */
    private function toEntities($models): array
    {
        return $models->map(fn($model) => $this->toEntity($model))->all();
    }
}