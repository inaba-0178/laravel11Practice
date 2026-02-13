<?php
namespace App\Infrastructure\Repositories\SelectBodyTypeList; 

use App\Domain\SelectBodyTypeList\Entities\CarSeriesBodyType;
use App\Domain\SelectBodyTypeList\Repositories\CarSeriesBodyTypeRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstCarSeriesBodyTypes;
use Illuminate\Support\Collection;

class EloquentCarSeriesBodyTypesRepository implements CarSeriesBodyTypeRepositoryInterface
{
    private MstCarSeriesBodyTypes $model;

    public function __construct(MstCarSeriesBodyTypes $model)
    {
        $this->model = $model;
    }

    public function findAll(): array
    {
        $carSeriesBodyTypes = $this->model
            ->get();

        return $this->toEntities($carSeriesBodyTypes);
    }

    public function findBySeriesBodyTypeId(int $BodyTypeId, array $conditions = []): Collection
    {
        $seriesBodyTypes = $this->model
            ->where("body_type_id", $BodyTypeId)
            ->get();
        
        return $seriesBodyTypes;
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param MstCarSeriesBodyTypes
     * @return CarSerieBodyTypes
     */
    private function toEntity(MstCarSeriesBodyTypes $model): CarSeriesBodyType
    {
        return new CarSeriesBodyType(
            $model->series_id,
            $model->series_name,
            $model->manufacturer_id,
        );
    }

    /**
     * Eloquentコレクションをエンティティ配列に変換
     * 
     * @param  $models
     * @return array
     */
    private function toEntities($models): array
    {
        return $models->map(function ($model) {
            return $this->toEntity($model);
        })->all();
    }

}