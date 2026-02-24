<?php
namespace App\Infrastructure\Repositories\PriceList; 

use App\Domain\PriceList\Entities\Price;
use App\Domain\PriceList\Repositories\PriceRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstPriceLists;
use App\Infrastructure\Repositories\BaseRepository;

class EloquentPriceListRepository extends BaseRepository implements PriceRepositoryInterface
{

    public function __construct(MstPriceLists $model)
    {
        parent::__construct($model);
    }

    public function findActive(): array
    {
        $regions = $this->model
            ->get();

        return $this->toEntities($regions, fn($model) => $this->toEntity($model));
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

}