<?php
namespace App\Domain\SelectBodyTypeList\Repositories;

use App\Domain\SelectBodyTypeList\Entities\BodyType;

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