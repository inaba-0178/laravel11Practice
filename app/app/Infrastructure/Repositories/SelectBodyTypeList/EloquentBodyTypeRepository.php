<?php
namespace App\Infrastructure\Repositories\SelectBodyTypeList;

use App\Domain\SelectBodyTypeList\Repositories\BodyTypeRepositoryInterface;
use App\Domain\SelectBodyTypeList\Entities\BodyType;
use App\Infrastructure\Eloquent\Mst\MstBodyTypes;

class EloquentBodyTypeRepository implements BodyTypeRepositoryInterface
{
    private MstBodyTypes $model;

    public function __construct(MstBodyTypes $model)
    {
        $this->model = $model;
    }

    public function findAll(): array
    {
        $bodyTypes = $this->model
            ->get();

        return $this->toEntities($bodyTypes);
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param MstBodyTypes
     * @return BodyType
     */
    private function toEntity(MstBodyTypes $model): BodyType
    {
        return new BodyType(
            $model->id,
            $model->name,
            $model->name_kana,
            $model->code,
            $model->description,
            $model->available_countries,
            $model->sort_order,
            $model->is_active,
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

    public function findByBodyType(string $code, array $conditions = []): BodyType
    {
        $manufacturer = $this->model::where('code', $code)
            ->first();

        return $this->toEntity($manufacturer);
    }
}