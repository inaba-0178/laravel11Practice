<?php
namespace App\Infrastructure\Repositories\PriceList; 

use App\Domain\PriceList\Entities\Price;
use App\Domain\PriceList\Repositories\PriceRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstPriceLists;
use App\Infrastructure\Repositories\BaseRepository;
use App\Domain\Common\Constants\CacheConstants;
use Illuminate\Support\Facades\Cache;

class EloquentPriceListRepository extends BaseRepository implements PriceRepositoryInterface
{

    public function __construct(MstPriceLists $model)
    {
        parent::__construct($model);
    }

    public function findActive(): array
    {
        return Cache::remember(CacheConstants::KEY_PRICE_LIST, CacheConstants::TTL_MST, function () {
            return $this->toEntities($this->model->get(), fn($model) => $this->toEntity($model));
        });
    }

    /**
     * EloquentモデルをEntityに変換
     * 
     * @param MstPriceLists $model
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