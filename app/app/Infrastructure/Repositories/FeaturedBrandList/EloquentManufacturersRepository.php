<?php
namespace App\Infrastructure\Repositories\FeaturedBrandList; 

use App\Domain\FeaturedBrandList\Entities\Manufacturer;
use App\Domain\FeaturedBrandList\Repositories\ManufacturerRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstManufacturers;

class EloquentManufacturersRepository implements ManufacturerRepositoryInterface
{
    private MstManufacturers $model;

    public function __construct(MstManufacturers $model)
    {
        $this->model = $model;
    }

    public function findAll(): array
    {
        $manufacturers = $this->model
            ->get();

        return $this->toEntities($manufacturers);
    }

    public function findActive(): array
    {
        $manufacturers = $this->model
            ->get();

        return $this->toEntities($manufacturers);
    }

    public function findById(int $id): ?Manufacturer
    {
        $manufacturer = $this->model
            ->find($id);

        if (!$manufacturer) {
            return null;
        }

        return $this->toEntity($manufacturer);
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param MstManufacturers
     * @return Manufacturer
     */
    private function toEntity(MstManufacturers $model): Manufacturer
    {
        return new Manufacturer(
            $model->id,
            $model->name,
            $model->name_kana,
            $model->display_name,
            $model->code,
            $model->url,
            $model->description,
            $model->country_code,
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

        //あなたの書いたfindByCodesは完璧！
    public function findByCodes(array $codes, array $conditions = []): array
    {
        return MstManufacturers::whereIn('code', $codes)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($m) => new Manufacturer(
                $m->id,
                $m->name,
                $m->name_kana,
                $m->display_name,
                $m->code,
                $m->url,
                $m->description,
                $m->country_code,
                $m->sort_order,
                $m->is_active,
            ))
            ->toArray();
    }
}