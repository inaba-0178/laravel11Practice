<?php
namespace App\Infrastructure\Repositories\SelectManufacturerList; 

use App\Domain\SelectManufacturerList\Repositories\ManufacturerRepositoryInterface;
use App\Infrastructure\Eloquent\Mst\MstManufacturers;

class EloquentManufacturersRepository implements ManufacturerRepositoryInterface
{
    private MstManufacturers $model;

    public function __construct(MstManufacturers $model)
    {
        $this->model = $model;
    }

    public function findByName(string $name): ?int
    {
        $manufacturer = $this->model::where('name', $name)
            ->first();
            
        return $manufacturer?->id;  // NULL安全演算子
    }
}