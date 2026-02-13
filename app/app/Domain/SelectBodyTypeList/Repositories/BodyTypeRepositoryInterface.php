<?php
namespace App\Domain\SelectBodyTypeList\Repositories;

use App\Domain\SelectBodyTypeList\Entities\BodyType;

interface BodyTypeRepositoryInterface
{
    /**
     * すべてのCarSerieを取得
     * 
     * @return CarSerie[]
     */
    public function findAll(): array;

    public function findByBodyType(string $code, array $conditions = []): BodyType;
}