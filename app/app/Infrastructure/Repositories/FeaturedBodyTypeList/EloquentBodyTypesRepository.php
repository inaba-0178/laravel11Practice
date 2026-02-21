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

    public function findByCodes(array $codes, array $conditions = []): array
    {
        $bodyTypes = $this->model
            ->whereIn('code', $codes)
            ->orderBy('sort_order')
            ->get();

        return $bodyTypes->map(fn (MstBodyTypes $bodyType) => $this->toEntity($bodyType))->all();
    }
    /**
     * EloquentモデルをEntityに変換
     * 
     * @param   MstBodyTypes
     * @return  BodyType
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
}