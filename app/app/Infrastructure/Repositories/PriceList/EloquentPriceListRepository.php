<?php
namespace App\Infrastructure\Repositories\PriceList; 

use App\Domain\PriceList\Entities\Price;
use App\Domain\PriceList\Repositories\PriceRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstPriceLists;

class EloquentPriceListRepository implements PriceRepositoryInterface
{
    private MstPriceLists $model;

    public function __construct(MstPriceLists $model)
    {
        $this->model = $model;
    }

    public function findAll(): array
    {
        $priceLists = $this->model
            ->get();

        return $this->toEntities($priceLists);
    }

    public function findActive(): array
    {
        $regions = $this->model
            ->get();

        return $this->toEntities($regions);
    }

    public function findById(int $id): ?Price
    {
        $price = $this->model
            ->find($id);

        if (!$price) {
            return null;
        }

        return $this->toEntity($price);
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param MstPriceLists
     * @return Price
     */
    private function toEntity(MstPriceLists $model): Price
    {
        return new Price(
            $model->id,
            $model->name,
            $model->max_amount,
            $model->is_unlimited,
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