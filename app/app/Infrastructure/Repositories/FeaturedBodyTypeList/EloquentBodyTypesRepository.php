<?php
namespace App\Infrastructure\Repositories\FeaturedBodyTypeList; 

use App\Domain\FeaturedBodyTypeList\Entities\BodyType;
use App\Domain\FeaturedBodyTypeList\Repositories\BodyTypeRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstBodyTypes;

class EloquentBodyTypesRepository implements BodyTypeRepositoryInterface
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

    public function findActive(): array
    {
        $bodyTypes = $this->model
            ->get();

        return $this->toEntities($bodyTypes);
    }

    public function findById(int $id): ?BodyType
    {
        $bodyType = $this->model
            ->find($id);

        if (!$bodyType) {
            return null;
        }

        return $this->toEntity($bodyType);
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

    public function findByCodes(array $codes, array $conditions = []): array
    {
        return MstBodyTypes::whereIn('code', $codes)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($m) => new BodyType(
                $m->id,
                $m->name,
                $m->name_kana,
                $m->code,
                $m->description,
                $m->availableCountries,
                $m->sort_order,
                $m->is_active,
            ))
            ->toArray();
    }
}