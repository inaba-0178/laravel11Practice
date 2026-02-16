<?php
namespace App\Domain\ManufacturerList\Repositories;

use App\Domain\ManufacturerList\Entities\Manufacturer;

interface ManufacturerRepositoryInterface
{
    /**
     * すべてのメーカーを取得
     * 
     * @return Manufacturer[]
     */
    public function findAll(): array;

    /**
     * 指定されたIDのメーカーを取得
     * 
     * @param int[] $ids
     * @return Manufacturer[]
     */
    public function findByIds(array $ids): array;
    
    /**
     * 単一のメーカーをIDで取得
     * 
     * @param int $id
     * @return Manufacturer|null
     */
    public function findById(int $id): ?Manufacturer;
}