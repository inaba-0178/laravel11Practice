<?php
namespace App\Infrastructure\Repositories\BodyTypeInfo;

use App\Domain\BodyTypeInfo\Repositories\BodyTypeRepositoryInterface;
use App\Domain\BodyTypeInfo\Entities\BodyType;
use App\Domain\BodyTypeInfo\Exceptions\BodyTypeNotFoundException;
use App\Infrastructure\Eloquent\Mst\MstBodyTypes;
use App\Domain\Common\Constants\CacheConstants;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EloquentBodyTypeRepository implements BodyTypeRepositoryInterface
{
    public function __construct(
        private readonly MstBodyTypes $model
    ) {}

    public function findAll(): array
    {
        return Cache::remember(CacheConstants::KEY_BODY_TYPES, CacheConstants::TTL_MST, function () {
            return $this->toEntities($this->model->get());
        });
    }

    /**
     * @throws BodyTypeNotFoundException
     */
    public function findByBodyType(string $code): BodyType
    {
        return Cache::remember(CacheConstants::KEY_BODY_TYPES . ':' . $code, CacheConstants::TTL_MST, function () use ($code) {
            $bodyType = $this->model
                ->where('code', $code)
                ->first();

            if ($bodyType === null) {
                throw new BodyTypeNotFoundException($code);
            }

            return $this->toEntity($bodyType);
        });
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