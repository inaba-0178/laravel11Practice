<?php
namespace App\Domain\BodyTypeInfo\Repositories;

use App\Domain\BodyTypeInfo\Entities\BodyType;

interface BodyTypeRepositoryInterface
{
    /**
     * すべてのBodyTypeを取得
     * 
     * @return BodyType[]
     */
    public function findAll(): array;

    public function findByBodyType(string $code, array $conditions = []): BodyType;
}