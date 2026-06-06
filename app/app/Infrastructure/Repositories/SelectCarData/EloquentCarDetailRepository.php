<?php
namespace App\Infrastructure\Repositories\SelectCarData;

use App\Domain\SelectCarData\Repositories\CarDetailRepositoryInterface;
use App\Domain\SelectCarData\Entities\CarDetail;
use App\Domain\SelectCarData\Exceptions\CarNotFoundException;
use App\Infrastructure\Eloquent\User\StkCarDetail;
use App\Infrastructure\Repositories\BaseRepository;

class EloquentCarDetailRepository extends BaseRepository implements CarDetailRepositoryInterface
{
    public function __construct(StkCarDetail $model)
    {
        parent::__construct($model);
    }

    /**
     * @throws CarNotFoundException
     */
    public function findByCarId(int $carId): CarDetail
    {
        $carDetail = $this->model
            ->where('car_id', $carId)
            ->first();

        if ($carDetail === null) {
            throw new CarNotFoundException($carId);
        }
        
        return $this->toEntity($carDetail);
    }

    private function toEntity(StkCarDetail $model): CarDetail
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
}