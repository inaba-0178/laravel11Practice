<?php
namespace App\Domain\SelectManufacturerList\Repositories;

interface CarSerieRepositoryInterface
{
    /**
     * すべてのCarSerieを取得
     * 
     * @return CarSerie[]
     */
    public function findAll(): array;

    public function findByManufacturerId(int $manufacturerId, array $conditions = []): array;
}