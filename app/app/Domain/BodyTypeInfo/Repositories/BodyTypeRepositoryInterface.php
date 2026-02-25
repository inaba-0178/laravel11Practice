<?php
namespace App\Domain\BodyTypeInfo\Repositories;

use App\Domain\BodyTypeInfo\Entities\BodyType;
use App\Domain\BodyTypeInfo\Exceptions\BodyTypeNotFoundException;

interface BodyTypeRepositoryInterface
{

    /**
     * コードでBodyTypeを取得
     * 
     * @param string $code
     * @return BodyType
     * @throws BodyTypeNotFoundException
     */
    public function findByBodyType(string $code): BodyType;
}