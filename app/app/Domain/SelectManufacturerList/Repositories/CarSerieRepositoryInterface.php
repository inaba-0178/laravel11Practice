<?php
namespace App\Domain\SelectManufacturerList\Repositories;

interface CarSerieRepositoryInterface
{
    /**
     * manufacturerIdの対象のCarSerieを取得
     * 
     * @return CarSerie[]
     */
    public function findByManufacturerId(int $manufacturerId, array $conditions = []): array;
}