<?php
namespace App\Domain\FeaturedBodyTypeList\Repositories;

interface BodyTypeRepositoryInterface
{
    /**
     * すべてのbodyTypeを取得
     * 
     * @return Manufacturer[]
     */
    public function findAll(): array;

    public function findByCodes(array $codes, array $conditions = []): array;
}