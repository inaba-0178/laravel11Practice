<?php
namespace App\Infrastructure\Repositories\SelectBodyTypeList; 

use App\Domain\SelectBodyTypeList\Repositories\CarSeriesBodyTypeRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstCarSeriesBodyTypes;
use Illuminate\Support\Collection;

class EloquentCarSeriesBodyTypesRepository implements CarSeriesBodyTypeRepositoryInterface
{
    private MstCarSeriesBodyTypes $model;

    public function __construct(MstCarSeriesBodyTypes $model)
    {
        $this->model = $model;
    }

    public function findBySeriesBodyTypeId(int $bodyTypeId, array $conditions = []): Collection
    {
        $seriesBodyTypes = $this->model
            ->where("body_type_id", $bodyTypeId)
            ->get();
        
        return $seriesBodyTypes;
    }

}