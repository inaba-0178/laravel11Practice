<?php

namespace App\Infrastructure\Repositories\SelectVehicleSpec;

use App\Domain\SelectVehicleSpec\Repositories\VehicleRepositoryInterface;
use App\Domain\SelectVehicleSpec\Entities\Vehicle;
use App\Infrastructure\Eloquent\Mst\MstVehicles;
use RuntimeException;

class EloquentVehicleRepository implements VehicleRepositoryInterface
{
    public function __construct(
        private readonly MstVehicles $model,
    ) {}

    public function findById(int $vehicleId): Vehicle
    {
        $result = $this->model
            ->where('id', $vehicleId)
            ->whereNull('deleted_at')
            ->first();

        if ($result === null) {
            throw new RuntimeException("車両情報が見つかりませんでした。vehicleId: {$vehicleId}");
        }

        return $this->toEntity($result);
    }

    private function toEntity(MstVehicles $model): Vehicle
    {
        return new Vehicle(
            id             : $model->id,
            seriesId       : $model->series_id,
            manufacturerId : $model->manufacturer_id,
            name           : $model->name,
            modelCode      : $model->model_code,
            bodyType       : $model->body_type,
            countryCode    : $model->country_code,
            status         : $model->status,
        );
    }
}