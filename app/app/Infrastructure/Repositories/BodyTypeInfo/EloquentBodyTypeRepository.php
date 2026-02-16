<?php
namespace App\Infrastructure\Repositories\BodyTypeInfo;

use App\Domain\BodyTypeInfo\Repositories\BodyTypeRepositoryInterface;
use App\Domain\BodyTypeInfo\Entities\BodyType;
use App\Domain\BodyTypeInfo\Exceptions\BodyTypeNotFoundException;
use App\Infrastructure\Eloquent\Mst\MstBodyTypes;
use Illuminate\Support\Collection;

class EloquentBodyTypeRepository implements BodyTypeRepositoryInterface
{
    public function __construct(
        private readonly MstBodyTypes $model
    ) {}

    public function findAll(): array
    {
        $bodyTypes = $this->model->get();
        return $this->toEntities($bodyTypes);
    }

    /**
     * @throws BodyTypeNotFoundException
     */
    public function findByBodyType(string $code): BodyType
    {
        $bodyType = $this->model
            ->where('code', $code)
            ->first();
        
        if ($bodyType === null) {
            throw new BodyTypeNotFoundException($code);
        }
        
        return $this->toEntity($bodyType);
    }

    private function toEntity(MstBodyTypes $model): BodyType
    {
        return new BodyType(
            id: $model->id,
            name: $model->name,
            nameKana: $model->name_kana,
            code: $model->code,
            description: $model->description,
            availableCountries: $model->available_countries,
            sortOrder: $model->sort_order,
            isActive: $model->is_active,
        );
    }

    /**
     * @return BodyType[]
     */
    private function toEntities(Collection $models): array
    {
        return $models->map(fn($model) => $this->toEntity($model))->all();
    }
}