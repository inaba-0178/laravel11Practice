<?php

namespace App\Infrastructure\Repositories\SelectVehicleSpec;

use App\Domain\SelectVehicleSpec\Repositories\VehicleVersionRepositoryInterface;
use App\Domain\SelectVehicleSpec\Entities\VehicleVersion;
use App\Infrastructure\Eloquent\Mst\MstVehicleYearVersions;
use App\Infrastructure\Repositories\BaseRepository;
use RuntimeException;

class EloquentVehicleVersionRepository extends BaseRepository implements VehicleVersionRepositoryInterface
{
    public function __construct(MstVehicleYearVersions $model)
    {
        parent::__construct($model);
    }

    public function findByVehicleId(int $vehicleId): VehicleVersion
    {
        $result = $this->model
            ->where('vehicle_id', $vehicleId)
            ->whereNull('deleted_at')
            ->orderBy('year_from', 'desc')
            ->first();

        if ($result === null) {
            throw new RuntimeException("車両年式情報が見つかりませんでした。vehicleId: {$vehicleId}");
        }

        return $this->toEntity($result);
    }

    private function toEntity(MstVehicleYearVersions $model): VehicleVersion
    {
        return new VehicleVersion(
            id                 : $model->id,
            vehicleId          : $model->vehicle_id,
            yearModel          : $model->year_from,
            displacementCc     : $model->displacement_cc,
            driveType          : $model->drive_type,
            fuelEfficiencyFrom : $model->fuel_efficiency_from ? (float)$model->fuel_efficiency_from : null,
            fuelEfficiencyTo   : $model->fuel_efficiency_to ? (float)$model->fuel_efficiency_to : null,
            maxPowerKw         : $model->max_power_kw,
            transmissionType   : $model->transmission_type,
            weightKg           : $model->weight_kg,
            isLatest           : (bool)$model->is_latest,
        );
    }
}