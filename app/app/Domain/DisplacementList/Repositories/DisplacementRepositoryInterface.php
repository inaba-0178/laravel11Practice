<?php
namespace App\Domain\DisplacementList\Repositories;

interface DisplacementRepositoryInterface
{
    /**
     * すべてのDisplacementを取得
     * 
     * @return Displacement[]
     */
    public function findAll(): array;
}