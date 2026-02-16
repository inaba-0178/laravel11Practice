<?php
namespace App\Domain\ManufacturerList\Repositories;

interface ManufacturerRepositoryInterface
{
    /**
     * すべてのmanufacturerを取得
     * 
     * @return Manufacturer[]
     */
    public function findAll(): array;

    public function findByIds(array $ids, array $conditions = []): array;
}