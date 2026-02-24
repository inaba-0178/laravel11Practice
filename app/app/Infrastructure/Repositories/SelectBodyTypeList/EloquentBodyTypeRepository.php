<?php
namespace App\Infrastructure\Repositories\SelectBodyTypeList;

use App\Domain\SelectBodyTypeList\Repositories\BodyTypeRepositoryInterface;
use App\Domain\SelectBodyTypeList\Entities\BodyType;
use App\Infrastructure\Eloquent\Mst\MstBodyTypes;
use App\Infrastructure\Repositories\BaseRepository;

class EloquentBodyTypeRepository extends BaseRepository implements BodyTypeRepositoryInterface
{

    public function __construct(MstBodyTypes $model)
    {
        parent::__construct($model);
    }

    public function findAll(): array
    {
        $bodyTypes = $this->model
            ->get();
        return $this->toEntities($bodyTypes, fn($model) => $this->toEntity($model));
    }

    public function findByBodyType(string $code, array $conditions = []): BodyType
    {
        $bodyType = $this->model::where('code', $code)
            ->first();
        
        if ($bodyType === null) {
            // 空のエンティティを返すか、例外を投げる
            return new BodyType(0, null, null, null, null, null, 0, 0);
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

}